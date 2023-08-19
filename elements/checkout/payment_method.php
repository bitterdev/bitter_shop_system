<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Error\ErrorList\Formatter\BootstrapFormatter;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var ErrorList|null $error */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var CheckoutService $cartService */
$cartService = $app->make(CheckoutService::class);
/** @var PaymentProviderService $paymentProviderService */
$paymentProviderService = $app->make(PaymentProviderService::class);

?>

<?php if ($error instanceof ErrorList && $error->has()) {
    $formatter = new BootstrapFormatter($error);
    echo $formatter->render();
} ?>

<div class="checkout step-select-payment-method">
    <h1>
        <?php echo t("Payment"); ?>
    </h1>

    <p>
        <?php echo t("Please select your preferred payment method."); ?>
    </p>

    <form action="#" method="post">
        <?php echo $token->output("payment_method"); ?>

        <?php foreach ($paymentProviderService->getAvailablePaymentProviders() as $paymentProvider) { ?>
            <div class="radio">
                <label>
                    <?php
                    $attributes = [];

                    if ($cartService->getSelectedPaymentProvider() instanceof PaymentProviderInterface && $cartService->getSelectedPaymentProvider()->getHandle() === $paymentProvider->getHandle()) {
                        $attributes["checked"] = "checked";
                    }
                    ?>

                    <?php echo $form->radio("selectedPaymentProvider", $paymentProvider->getHandle(), 1, $attributes); ?>

                    <?php echo $paymentProvider->getName(); ?>
                </label>
            </div>
        <?php } ?>

        <div class="float-end">
            <a href="<?php echo Url::to(Page::getCurrentPage(), "contact_information"); ?>" class="btn btn-secondary"
               rel="nofollow">
                <i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo t("Back"); ?>
            </a>

            <button type="submit" class="btn btn-primary">
                <?php echo t("Next"); ?>

                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>
