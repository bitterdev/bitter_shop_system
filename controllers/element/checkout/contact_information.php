<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Checkout;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Controller;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;

class ContactInformation extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return '/checkout/contact_information';
    }

    public function view()
    {
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        /** @var CheckoutService $cartService */
        $cartService = $this->app->make(CheckoutService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $this->app->make(UserInfoRepository::class);
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        $categoryEntity = $service->getByHandle('customer');
        /** @var CustomerCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        $user = new User();
        $errorList = new ErrorList();

        /** @var CustomerKey[] $attributes */
        $attributes = [];

        if ($config->get("concrete.user.registration.enabled") &&
            $user->isRegistered() &&
            count($setManager->getUnassignedAttributeKeys()) === 0) {
            $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "payment_method"), Response::HTTP_TEMPORARY_REDIRECT)->send();
            $this->app->shutdown();
        }

        if ($this->request->getMethod() === "POST" && $token->validate("submit_contact_information")) {
            $formValidator->setData($this->request->request->all());

            if (!$user->isRegistered()) {
                $formValidator->addRequiredEmail("email", t("You need to enter a valid mail address."));
            }

            if ($formValidator->test()) {
                if (!$user->isRegistered()) {
                    // check if there is already a user account associated with the mail address
                    if ($userInfoRepository->getByEmail($this->request->request->get("email")) instanceof UserInfo) {
                        $errorList->add(t("You have already an existing user account. Please login first to continue the checkout process."));
                        $this->set("error", $errorList);
                    }
                }

                if ($cartService->getSelectedCheckoutMethod() === "register") {
                    $password = $this->request->request->get("password");
                    $passwordRepeat = $this->request->request->get("passwordRepeat");

                    $this->app->make('validator/password')->isValid($password, $errorList);

                    if ($password !== $passwordRepeat) {
                        $errorList->add(t('The two passwords provided do not match.'));
                    }

                    if (!$config->get('concrete.user.registration.email_registration')) {
                        $username = $this->request->request->get("username");
                        $this->app->make('validator/user/name')->isValid($username, $errorList);
                    }
                }

                if (!$errorList->has()) {
                    foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                        /** @var CustomerKey $ak */
                        $controller = $ak->getController();

                        if (method_exists($controller, 'validateForm')) {
                            $controller->setRequest($this->request);
                            $validateResponse = $controller->validateForm($controller->post());

                            if (!$validateResponse) {
                                $errorList->add(t("The field \"%s\" is required.", $ak->getAttributeKeyName()));
                            }
                        }
                    }
                }

                if (!$errorList->has()) {
                    if (!$user->isRegistered()) {
                        $cartService->setCustomerMailAddress($this->request->request->get("email"));

                        if ($cartService->getSelectedCheckoutMethod() === "register") {
                            $cartService->setCustomerPassword($this->request->request->get("password"));

                            if (!$config->get('concrete.user.registration.email_registration')) {
                                $cartService->setCustomerUsername($this->request->request->get("username"));
                            }
                        }
                    }

                    foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                        /** @var CustomerKey $ak */
                        $controller = $ak->getController();
                        $cartService->setCustomerAttribute($ak->getAttributeKeyHandle(), $controller->createAttributeValueFromRequest());
                    }

                    $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "payment_method"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                    $this->app->shutdown();
                }
            } else {
                $errorList = $formValidator->getError();
            }
        }

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            $attributes[] = $ak;
        }

        $this->set("error", $errorList);
        $this->set('attributes', $attributes);
        $this->set('renderer', new Renderer(new FrontendFormContext(), $cartService));
        $this->set('email', $cartService->getCustomerMailAddress());
        $this->set('username', $cartService->getCustomerUsername());
        $this->set('checkoutMethod', $cartService->getSelectedCheckoutMethod());
        $this->set('password', $cartService->getCustomerPassword());
    }
}
