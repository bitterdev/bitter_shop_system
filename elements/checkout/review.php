<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Coupon\CouponService;
use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Error\ErrorList\Formatter\BootstrapFormatter;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Captcha\CaptchaInterface;

/** @var bool $displayCaptcha */
/** @var ErrorList|null $error */
/** @var Page|null $termsOfUsePage */
/** @var Page|null $privacyPolicyPage */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var CheckoutService $cartService */
$cartService = $app->make(CheckoutService::class);
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var CaptchaInterface $captcha */
$captcha = $app->make(CaptchaInterface::class);
/** @var CouponService $couponService */
$couponService = $app->make(CouponService::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
$includeTax = $config->get("bitter_shop_system.display_prices_including_tax", false);
?>

<?php if ($error instanceof ErrorList && $error->has()) {
    $formatter = new BootstrapFormatter($error);
    echo $formatter->render();
} ?>

<div class="checkout step-summary">
    <h1>
        <?php echo t("Review"); ?>
    </h1>

    <p>
        <?php echo t("Please review and submit your order."); ?>
    </p>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>
                <?php echo t("Product"); ?>
            </th>

            <th class="text-right">
                <?php echo t("Quantity"); ?>
            </th>

            <th class="text-right">
                <?php echo t("Unit Price"); ?>
            </th>

            <th class="text-right">
                <?php echo t("Total Price"); ?>
            </th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($cartService->getAllItems() as $cartItem) { ?>
            <tr>
                <td>
                    <?php echo $cartItem->getProduct()->getName(); ?>
                </td>

                <td class="text-right">
                    <?php echo $cartItem->getQuantity(); ?>
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartItem->getProduct()->getPrice($includeTax)); ?>
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartItem->getSubtotal($includeTax)); ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($cartService->getHighestShippingCostEntry() instanceof ShippingCost) { ?>
            <tr>
                <td>
                    <?php echo t("Shipping"); ?>
                </td>

                <td class="text-right">
                    1
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getHighestShippingCost($includeTax)); ?>
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getHighestShippingCost($includeTax)); ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($cartService->getCoupon() instanceof Coupon) { ?>
            <tr class="text-danger">
                <td>
                    <?php echo t("Discount Code: %s", $cartService->getCoupon()->getCode()); ?>
                </td>

                <td class="text-right">
                    1
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getDiscount($includeTax) * -1); ?>
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getDiscount($includeTax) * -1); ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <th colspan="3">
                <div class="float-end">
                    <?php echo t("Subtotal"); ?>
                </div>
            </th>

            <th class="text-right">
                <?php echo $moneyTransformer->transform($includeTax ? $cartService->getTotal() : $cartService->getSubtotal()); ?>
            </th>
        </tr>

        <?php if ($cartService->getTax() > 0) { ?>
            <tr>
                <th colspan="3">
                    <div class="float-end">
                        <?php echo $includeTax ? t("Include Tax") : t("Exclude Tax"); ?>
                    </div>
                </th>

                <th class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getTax()); ?>
                </th>
            </tr>
        <?php } ?>

        <tr>
            <th colspan="3">
                <div class="float-end">
                    <?php echo t("Total"); ?>
                </div>
            </th>

            <th class="text-right">
                <?php echo $moneyTransformer->transform($cartService->getTotal()); ?>
            </th>
        </tr>
        </tbody>
    </table>

    <?php if (count($couponService->getAll()) > 0) { ?>
        <form action="<?php echo Url::to(Page::getCurrentPage(), "redeem_coupon"); ?>">
            <div class="form-group">
                <?php echo $form->label("couponCode", t("Coupon Code")); ?>

                <div class="input-group">
                    <?php echo $form->text("couponCode"); ?>

                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-sm">
                            <?php echo t("Redeem Coupon"); ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>

    <form action="<?php echo Url::to(Page::getCurrentPage(), "review"); ?>" method="post">
        <?php echo $token->output("create_order"); ?>

        <?php if ($privacyPolicyPage instanceof Page && !$privacyPolicyPage->isError()) { ?>
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("acceptPrivacyPolicy", 1); ?>

                    <?php /** @noinspection HtmlUnknownTarget */
                    echo t("I accept the %s.", sprintf(
                        "<a href=\"%s\" target='_blank'>%s</a>",
                        Url::to($privacyPolicyPage),
                        t("privacy policy")
                    )); ?>
                </label>
            </div>
        <?php } ?>

        <?php if ($termsOfUsePage instanceof Page && !$termsOfUsePage->isError()) { ?>
            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("acceptTermsOfUse", 1); ?>

                    <?php /** @noinspection HtmlUnknownTarget */
                    echo t("I accept the %s.", sprintf(
                        "<a href=\"%s\" target='_blank'>%s</a>",
                        Url::to($termsOfUsePage),
                        t("terms of use")
                    )); ?>
                </label>
            </div>
        <?php } ?>

        <?php if ($displayCaptcha) { ?>
            <div class="form-group captcha">
                <?php $captchaLabel = $captcha->label(); ?>

                <?php if (!empty($captchaLabel)) { ?>
                    <?php echo $form->label('', $captcha->label()); ?>
                <?php } ?>

                <div>
                    <?php $captcha->display(); ?>
                </div>

                <div>
                    <?php $captcha->showInput(); ?>
                </div>
            </div>
        <?php } ?>

        <div class="float-end">
            <a href="<?php echo Url::to(Page::getCurrentPage(), "payment_method"); ?>" class="btn btn-secondary"
               rel="nofollow">
                <i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo t("Back"); ?>
            </a>

            <button type="submit" class="btn btn-primary">
                <?php echo t("Complete Order"); ?>

                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>