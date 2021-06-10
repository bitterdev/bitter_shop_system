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

use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var $entry TaxRate */
/** @var $form Form */
/** @var $token Token */

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "bitter_shop_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/bitter-shop-system/reviews"], 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');
$isEdit = is_numeric($entry->getId());
?>

    <form action="#" method="post">
        <?php echo $token->output("save_tax_rate_entity"); ?>

        <fieldset>
            <legend>
                <?php echo t("General"); ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label(
                    "name",
                    t("Name"),
                    [
                        "class" => "control-label"
                    ]
                ); ?>

                <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

                <?php echo $form->text(
                    "name",
                    $entry->getName(),
                    [
                        "class" => "form-control",
                        "required" => "required",
                        "max-length" => "255",
                    ]
                ); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label(
                    "handle",
                    t("Handle"),
                    [
                        "class" => "control-label"
                    ]
                ); ?>

                <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

                <?php echo $form->text(
                    "handle",
                    $entry->getHandle(),
                    [
                        "class" => "form-control",
                        "required" => "required",
                        "max-length" => "255",
                    ]
                ); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label(
                    "rate",
                    t("Rate"),
                    [
                        "class" => "control-label"
                    ]
                ); ?>

                <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

                <div class="input-group">
                    <?php echo $form->number(
                        "rate",
                        $entry->getRate(),
                        [
                            "class" => "form-control",
                            "required" => "required",
                            "max-length" => "255",
                        ]
                    ); ?>

                    <span class="input-group-addon">
                <?php echo t('%'); ?>
            </span>
                </div>
            </div>
        </fieldset>

        <?php if ($isEdit) { ?>
            <fieldset>
                <legend>
                    <?php echo t("Variants"); ?>
                </legend>

                <?php if (count($entry->getVariants()) > 0) { ?>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                <?php echo t("Country"); ?>
                            </th>

                            <th>
                                <?php echo t("State"); ?>
                            </th>

                            <th>
                                <?php echo t("Rate"); ?>
                            </th>

                            <th>
                                &nbsp;
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($entry->getVariants() as $variant) { ?>
                            <tr>
                                <td>
                                    <?php echo $variant->getCountry(); ?>
                                </td>

                                <td>
                                    <?php echo $variant->getState(); ?>
                                </td>

                                <td>
                                    <?php echo sprintf("%s%%", $variant->getRate()); ?>
                                </td>

                                <td>
                                    <div class="pull-right">
                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/tax_rates/remove_variant", $variant->getId()); ?>" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo t('Remove'); ?>
                                        </a>

                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/tax_rates/edit_variant", $variant->getId()); ?>" class="btn btn-default btn-sm">
                                            <i class="fa fa-pencil" aria-hidden="true"></i> <?php echo t('Edit'); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <?php echo t("If you want to have individual rates based on the users location you can do so by creating variants."); ?>
                <?php } ?>
            </fieldset>
        <?php } ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/bitter_shop_system/tax_rates"); ?>" class="btn btn-default">
                    <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
                </a>

                <div class="pull-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
                    </button>
                </div>
            </div>
    </form>

<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');