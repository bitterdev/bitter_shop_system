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

use Bitter\BitterShopSystem\Entity\TaxRateVariant;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var TaxRateVariant $variant */
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);




?>
    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>
    </div>

<form action="#" method="post">
    <?php echo $token->output("save_tax_rate_variant"); ?>

    <div class="form-group ccm-attribute-address-line ccm-attribute-address-country">
        <?php echo $form->label("country", t("Country")); ?>

        <?php echo $form->selectCountry("country", $variant->getCountry(), [
            'linkStateProvinceField' => true,
            'hideUnusedStateProvinceField' => true,
            'clearStateProvinceOnChange' => true,
        ]); ?>
    </div>

    <div class="form-group ccm-attribute-address-line ccm-attribute-address-state-province" data-countryfield="country">
        <?php echo $form->label("state_province", t('State/Province')); ?>
        <?php echo $form->text("state_province", $variant->getState()); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("variantRate", t("Rate"), ["class" => "control-label"]); ?>

        <div class="input-group">
            <?php echo $form->number("variantRate", $variant->getRate(), [
                "class" => "form-control",
                "required" => "required",
                "min" => "0",
                "max" => 100,
                "step" => "any"
            ]); ?>

            <div class="input-group-text">
                <?php echo t('%'); ?>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/bitter_shop_system/tax_rates/edit", $variant->getTaxRate()->getId()); ?>" class="btn btn-secondary">
                <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
            </a>

            <div class="float-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
                </button>
            </div>
        </div>
</form>

<script>
    var ccm_attributeTypeAddressStates;

    ccm_attributeTypeAddressSelectCountry = function (cls, country) {
        var ss = $('.' + cls + ' .ccm-attribute-address-state-province select');
        var si = $('.' + cls + ' .ccm-attribute-address-state-province input[type=text]');

        var foundStateList = false;
        ss.html("");
        for (j = 0; j < ccm_attributeTypeAddressStates.length; j++) {
            var sa = ccm_attributeTypeAddressStates[j].split(':');
            if (jQuery.trim(sa[0]) == country) {
                if (!foundStateList) {
                    foundStateList = true;
                    si.attr('name', 'inactive_' + si.attr('ccm-attribute-address-field-name'));
                    si.hide();
                    ss.append('<option value="">Choose State/Province</option>');
                }
                ss.show();
                ss.attr('name', si.attr('ccm-attribute-address-field-name'));
                ss.append('<option value="' + jQuery.trim(sa[1]) + '">' + jQuery.trim(sa[2]) + '</option>');
            }
        }

        if (!foundStateList) {
            ss.attr('name', 'inactive_' + si.attr('ccm-attribute-address-field-name'));
            ss.hide();
            si.show();
            si.attr('name', si.attr('ccm-attribute-address-field-name'));
        }
    }

    ccm_setupAttributeTypeAddressSetupStateProvinceSelector = function (cls) {
        var cs = $('.' + cls + ' .ccm-attribute-address-country select');
        cs.change(function () {
            var v = $(this).val();
            ccm_attributeTypeAddressSelectCountry(cls, v);
        });

        if (cs.attr('ccm-passed-value') != '') {
            $(function () {
                cs.find('option[value="' + cs.attr('ccm-passed-value') + '"]').attr('selected', true);
                ccm_attributeTypeAddressSelectCountry(cls, cs.attr('ccm-passed-value'));
                var ss = $('.' + cls + ' .ccm-attribute-address-state-province select');
                if (ss.attr('ccm-passed-value') != '') {
                    ss.find('option[value="' + ss.attr('ccm-passed-value') + '"]').attr('selected', true);
                }
            });
        }
    }
</script>


<?php
