<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Checkout;

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;

class CheckoutMethod extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return '/checkout/checkout_method';
    }

    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $user = new User();

        if (!$config->get("concrete.user.registration.enabled") || $user->isRegistered()) {
            $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "contact_information"), Response::HTTP_TEMPORARY_REDIRECT)->send();
            $this->app->shutdown();
        }

        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        /** @var CheckoutService $cartService */
        $cartService = $this->app->make(CheckoutService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var PostLoginLocation $postLoginLocation */
        $postLoginLocation = $this->app->make(PostLoginLocation::class);
        /** @var Token $token */
        $token = $this->app->make(Token::class);

        if ($this->request->getMethod() === "POST" && $token->validate("checkout_method")) {
            $formValidator->setData($this->request->request->all());

            $formValidator->addRequired("selectedCheckoutMethod", t("You need to select a checkout method."));

            if ($formValidator->test()) {
                $cartService->setSelectedCheckoutMethod($this->request->request->get("selectedCheckoutMethod", "guest"));

                if ($this->request->request->get("selectedCheckoutMethod") === "login") {
                    $postLoginLocation->setSessionPostLoginUrl((string)Url::to(Page::getCurrentPage(), "contact_information"));
                    $responseFactory->redirect((string)Url::to("/login"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                } else {
                    $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "contact_information"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                }

                $this->app->shutdown();
            } else {
                $this->set("error", $formValidator->getError());
            }
        }
    }
}
