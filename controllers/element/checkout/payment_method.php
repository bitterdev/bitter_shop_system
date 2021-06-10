<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Checkout;

use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

class PaymentMethod extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return '/checkout/payment_method';
    }

    public function view()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var CheckoutService $cartService */
        $cartService = $this->app->make(CheckoutService::class);
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        /** @var PaymentProviderService $paymentProviderService */
        $paymentProviderService = $this->app->make(PaymentProviderService::class);
        /** @var Token $token */
        $token = $this->app->make(Token::class);

        if ($this->request->getMethod() === "POST" && $token->validate("payment_method")) {
            $formValidator->setData($this->request->request->all());

            $formValidator->addRequired("selectedPaymentProvider", t("You need to select a payment method."));

            if ($formValidator->test()) {
                $paymentProvider = $paymentProviderService->getByHandle($this->request->request->get("selectedPaymentProvider"));

                if ($paymentProvider instanceof PaymentProviderInterface) {
                    $cartService->setSelectedPaymentProvider($paymentProvider);
                    $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "review"), Response::HTTP_TEMPORARY_REDIRECT)->send();
                    $this->app->shutdown();
                } else {
                    $errorList = new ErrorList();
                    $errorList->add(t("The selected payment method is invalid."));
                    $this->set("error", $errorList);
                }
            } else {
                $this->set("error", $formValidator->getError());
            }
        }
    }
}
