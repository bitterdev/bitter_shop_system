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
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

class Review extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    /** @var Page|null */
    protected $termsOfUsePage;
    /** @var Page|null */
    protected $privacyPolicyPage;
    /** @var bool */
    protected $displayCaptcha;

    /**
     * @return Page|null
     */
    public function getTermsOfUsePage(): ?Page
    {
        return $this->termsOfUsePage;
    }

    /**
     * @param Page|null $termsOfUsePage
     * @return Review
     */
    public function setTermsOfUsePage(?Page $termsOfUsePage): Review
    {
        $this->termsOfUsePage = $termsOfUsePage;
        return $this;
    }

    /**
     * @return Page|null
     */
    public function getPrivacyPolicyPage(): ?Page
    {
        return $this->privacyPolicyPage;
    }

    /**
     * @param Page|null $privacyPolicyPage
     * @return Review
     */
    public function setPrivacyPolicyPage(?Page $privacyPolicyPage): Review
    {
        $this->privacyPolicyPage = $privacyPolicyPage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayCaptcha(): bool
    {
        return $this->displayCaptcha;
    }

    /**
     * @param bool $displayCaptcha
     * @return Review
     */
    public function setDisplayCaptcha(bool $displayCaptcha): Review
    {
        $this->displayCaptcha = $displayCaptcha;
        return $this;
    }

    public function getElement(): string
    {
        return '/checkout/review';
    }

    public function view()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        /** @var CheckoutService $cartService */
        $cartService = $this->app->make(CheckoutService::class);
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var CaptchaInterface $captcha */
        $captcha = $this->app->make(CaptchaInterface::class);

        $errorList = $this->get("error") instanceof ErrorList ? $this->get("error") : new ErrorList();

        if (!$cartService->hasPaymentProviderSelect()) {
            $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "payment_method"), Response::HTTP_TEMPORARY_REDIRECT)->send();
            $this->app->shutdown();
        }

        if ($this->request->getMethod() === "POST" && $token->validate("create_order")) {
            $formValidator->setData($this->request->request->all());

            if ($this->getPrivacyPolicyPage() instanceof Page && !$this->getPrivacyPolicyPage()->isError()) {
                $formValidator->addRequired("acceptPrivacyPolicy", t("You need to accept the privacy policy."));
            }

            if ($this->getTermsOfUsePage() instanceof Page && !$this->getTermsOfUsePage()->isError()) {
                $formValidator->addRequired("acceptTermsOfUse", t("You need to accept the terms of use."));
            }

            if ($formValidator->test()) {
                if ($this->isDisplayCaptcha()) {
                    if (!$captcha->check()) {
                        $errorList->add(t("Invalid captcha code."));
                    }
                }

                if (!$errorList->has()) {
                    // temporary save the checkout page
                    $cartService->setCheckoutPageId(Page::getCurrentPage()->getCollectionID());
                    $cartService->getSelectedPaymentProvider()->processPayment();

                    $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "complete"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                    $this->app->shutdown();
                }

            } else {
                $errorList = $formValidator->getError();
            }
        }

        $this->set("error", $errorList);
        $this->set("displayCaptcha", $this->isDisplayCaptcha());
        $this->set("termsOfUsePage", $this->getTermsOfUsePage());
        $this->set("privacyPolicyPage", $this->getPrivacyPolicyPage());
    }
}
