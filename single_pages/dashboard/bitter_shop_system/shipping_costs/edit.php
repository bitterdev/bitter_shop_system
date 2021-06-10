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

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var array $taxRateValues */
/** @var $entry ShippingCost */
/** @var $form Form */
/** @var $token Token */
$app = Application::getFacadeApplication();
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "bitter_shop_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/bitter-shop-system/reviews"], 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');
$isEdit = is_numeric($entry->getId());

?>
    <form action="#" method="post">
        <?php echo $token->output("save_shipping_cost_entity"); ?>

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
                    "price",
                    t("Price"),
                    [
                        "class" => "control-label"
                    ]
                ); ?>

                <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

                <div class="input-group">
                    <?php echo $form->number(
                        "price",
                        $entry->getPrice(),
                        [
                            "class" => "form-control",
                            "required" => "required",
                            "max-length" => "255",
                        ]
                    ); ?>

                    <span class="input-group-addon">
                <?php echo $config->get("bitter_shop_system.money_formatting.currency_symbol", "$"); ?>
            </span>
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
                                <?php echo t("Price"); ?>
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
                                    <?php echo $moneyTransformer->transform($variant->getPrice()); ?>
                                </td>

                                <td>
                                    <div class="pull-right">
                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/shipping_costs/remove_variant", $variant->getId()); ?>" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo t('Remove'); ?>
                                        </a>

                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/shipping_costs/edit_variant", $variant->getId()); ?>" class="btn btn-default btn-sm">
                                            <i class="fa fa-pencil" aria-hidden="true"></i> <?php echo t('Edit'); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <?php echo t("If you want to have individual prices based on the users location you can do so by creating variants."); ?>
                <?php } ?>
            </fieldset>
        <?php } ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/bitter_shop_system/shipping_costs"); ?>"
                   class="btn btn-default">
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