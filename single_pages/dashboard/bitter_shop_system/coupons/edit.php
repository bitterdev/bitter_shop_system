<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var array $taxRateValues */
/** @var $entry Coupon */
/** @var $form Form */
/** @var $token Token */

$app = Application::getFacadeApplication();
/** @var \Concrete\Core\Form\Service\Widget\DateTime $dateWidget */
$dateWidget = $app->make(\Concrete\Core\Form\Service\Widget\DateTime::class);
/** @var Repository $config */
$config = $app->make(Repository::class);

?>

    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>
    </div>

    <?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "bitter_shop_system"); ?>
    
    <form action="#" method="post">
        <?php echo $token->output("save_coupon_entity"); ?>

        <div class="form-group">
            <?php echo $form->label(
                "code",
                t("Code"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="text-muted small">
                <?php echo t('Required') ?>
            </div>

            <?php echo $form->text(
                "code",
                $entry->getCode(),
                [
                    "class" => "form-control",
                    "required" => "required",
                    "max-length" => "255",
                ]
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "validFrom",
                t("Valid From"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php echo $dateWidget->datetime("validFrom", $entry->getValidFrom()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "validTo",
                t("Valid To"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php echo $dateWidget->datetime("validTo", $entry->getValidTo()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "usePercentageDiscount",
                t("Use Percentage Discount"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("usePercentageDiscount", 1, $entry->isUsePercentageDiscount()); ?>

                    <?php echo t("Use Percentage Discount"); ?>
                </label>
            </div>
        </div>

        <div class="form-group" id="discount-price-container">
            <?php echo $form->label(
                "discountPrice",
                t("Discount Price"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="input-group">
                <?php echo $form->number(
                    "discountPrice",
                    $entry->getDiscountPrice(),
                    [
                        "class" => "form-control",
                        "min" => 0,
                        "step" => 0.1
                    ]
                ); ?>
                <div class="input-group-text">
                    <?php echo $config->get("bitter_shop_system.money_formatting.currency_symbol", "$"); ?>
                </div>
            </div>
        </div>

        <div class="form-group" id="discount-percentage-container">
            <?php echo $form->label(
                "discountPercentage",
                t("Discount Percentage"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="input-group">
                <?php echo $form->number(
                    "discountPercentage",
                    $entry->getDiscountPercentage(),
                    [
                        "class" => "form-control",
                        "min" => 0,
                        "step" => 0.1
                    ]
                ); ?>
                <div class="input-group-text">
                    <?php echo t('%'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "maximumDiscountAmount",
                t("Maximum Discount Amount"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="input-group">
                <?php echo $form->number(
                    "maximumDiscountAmount",
                    $entry->getMaximumDiscountAmount(),
                    [
                        "class" => "form-control",
                        "min" => 0,
                        "step" => 0.1
                    ]
                ); ?>
                <div class="input-group-text">
                    <?php echo $config->get("bitter_shop_system.money_formatting.currency_symbol", "$"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "minimumOrderAmount",
                t("Minimum Order Amount"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="input-group">
                <?php echo $form->number(
                    "minimumOrderAmount",
                    $entry->getMinimumOrderAmount(),
                    [
                        "class" => "form-control",
                        "min" => 0,
                        "step" => 0.1
                    ]
                ); ?>

                <div class="input-group-text">
                    <?php echo $config->get("bitter_shop_system.money_formatting.currency_symbol", "$"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "limitQuantity",
                t("Limit Quantity"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("limitQuantity", 1, $entry->isLimitQuantity()); ?>

                    <?php echo t("Limit Quantity"); ?>
                </label>
            </div>
        </div>

        <div class="form-group" id="quantity-container">
            <?php echo $form->label(
                "quantity",
                t("Quantity"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php echo $form->number(
                "quantity",
                $entry->getQuantity(),
                [
                    "class" => "form-control",
                    "min" => 0,
                    "step" => 1
                ]
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "excludeDiscountedProducts",
                t("Exclude Discounted Products"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="checkbox">
                <label>
                    <?php echo $form->checkbox("excludeDiscountedProducts", 1, $entry->isExcludeDiscountedProducts()); ?>

                    <?php echo t("Exclude Discounted Products"); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "taxRate",
                t("Tax Rate"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php if (empty($taxRateValues)): ?>
                <p>
                    <?php echo t("No entries available."); ?>
                </p>
            <?php else: ?>

                <?php echo $form->select(
                    "taxRate",
                    $taxRateValues,
                    $entry->getTaxRate() instanceof TaxRate && $entry->getTaxRate()->getId(),
                    [
                        "class" => "form-control",
                        "max-length" => "255",
                    ]
                ); ?>

            <?php endif; ?>

        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/bitter_shop_system/coupons"); ?>" class="btn btn-secondary">
                    <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
                </a>

                <div class="float-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        (function ($) {
            $(function () {
                $("#limitQuantity").change(function () {
                    if ($(this).is(":checked")) {
                        $("#quantity-container").removeClass("hidden");
                    } else {
                        $("#quantity-container").addClass("hidden");
                    }
                }).trigger("change");

                $("#usePercentageDiscount").change(function () {
                    if ($(this).is(":checked")) {
                        $("#discount-percentage-container").removeClass("hidden");
                        $("#discount-price-container").addClass("hidden");
                    } else {
                        $("#discount-percentage-container").addClass("hidden");
                        $("#discount-price-container").removeClass("hidden");
                    }
                }).trigger("change");
            });
        })(jQuery);
    </script>
<?php
