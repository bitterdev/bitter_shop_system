<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

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

<div class="checkout step-select-checkout-method">
    <h1>
        <?php echo t("Checkout Method"); ?>
    </h1>

    <p>
        <?php echo t("Please select the checkout method."); ?>
    </p>

    <form action="#" method="post">
        <?php echo $token->output("checkout_method"); ?>

        <div class="radio">
            <label>
                <?php echo $form->radio("selectedCheckoutMethod", "login", $cartService->getSelectedCheckoutMethod()); ?>
                <?php echo t("Login with existing account"); ?>
            </label>
        </div>

        <div class="radio">
            <label>
                <?php echo $form->radio("selectedCheckoutMethod", "register", $cartService->getSelectedCheckoutMethod()); ?>
                <?php echo t("Create a new account"); ?>
            </label>
        </div>

        <div class="radio">
            <label>
                <?php echo $form->radio("selectedCheckoutMethod", "guest", $cartService->getSelectedCheckoutMethod() ?? "guest"); ?>
                <?php echo t("Checkout as guest"); ?>
            </label>
        </div>

        <div class="float-end">
            <button type="submit" class="btn btn-primary">
                <?php echo t("Next"); ?>

                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>
