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

use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\View\View;

/** @var array $sites */
/** @var array $locales */
/** @var Renderer $renderer */
/** @var ProductKey[] $attributes */

/** @var array $taxRateValues */
/** @var array $categoryValues */
/** @var array $shippingCostValues */
/** @var $entry Product */
/** @var $form Form */
/** @var $token Token */

$app = Application::getFacadeApplication();
/** @var FileManager $fileSelector */
$fileSelector = $app->make(FileManager::class);
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var Repository $config */
$config = $app->make(Repository::class);

$isEdit = is_numeric($entry->getId());
?>

    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>

        <?php if ($isEdit) { ?>
            <a href="<?php echo Url::to(\Concrete\Core\Page\Page::getCurrentPage(), "add_variant", $entry->getId()); ?>" class="btn btn-success">
                <?php echo t("Add Variant"); ?>
            </a>
        <?php } ?>
    </div>


    <form action="#" method="post">
        <?php echo $token->output("save_product_entity"); ?>

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
                "locale",
                t("Locale"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

            <?php echo $form->select(
                "locale",
                $locales,
                $entry->getLocale(),
                [
                    "class" => "form-control",
                    "required" => "required"
                ]
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "shortDescription",
                t("Short Description"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

            <?php echo $form->textarea(
                "shortDescription",
                $entry->getShortDescription(),
                [
                    "class" => "form-control",
                    "required" => "required",
                    "max-length" => "255",
                ]
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "description",
                t("Description"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

            <?php echo $editor->outputStandardEditor(
                "description",
                $entry->getDescription()
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "priceRegular",
                t("Price Regular"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

            <div class="input-group">
                <?php echo $form->number(
                    "priceRegular",
                    $entry->getPriceRegular(),
                    [
                        "class" => "form-control",
                        "required" => "required",
                        "max-length" => "255",
                        "step" => "any"
                    ]
                ); ?>

                <span class="input-group-text">
                <?php echo $config->get("bitter_shop_system.money_formatting.currency_symbol", "$"); ?>
            </span>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "priceDiscounted",
                t("Price Discounted"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <div class="input-group">
                <?php echo $form->number(
                    "priceDiscounted",
                    $entry->getPriceDiscounted(),
                    [
                        "class" => "form-control",
                        "max-length" => "255",
                        "step" => "any"
                    ]
                ); ?>

                <span class="input-group-text">
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

        <div class="form-group">
            <?php echo $form->label(
                "shippingCost",
                t("Shipping Cost"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php if (empty($shippingCostValues)): ?>
                <p>
                    <?php echo t("No entries available."); ?>
                </p>
            <?php else: ?>
                <?php echo $form->select(
                    "shippingCost",
                    $shippingCostValues,
                    $entry->getShippingCost() instanceof ShippingCost && $entry->getShippingCost()->getId(),
                    [
                        "class" => "form-control",
                        "max-length" => "255",
                    ]
                ); ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "category",
                t("Category"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php if (empty($categoryValues)): ?>
                <p>
                    <?php echo t("No entries available."); ?>
                </p>
            <?php else: ?>
                <?php echo $form->select(
                    "category",
                    $categoryValues,
                    $entry->getCategory() instanceof Category && $entry->getCategory()->getId(),
                    [
                        "class" => "form-control",
                        "max-length" => "255",
                    ]
                ); ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
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
                    "min" => 0,
                    "class" => "form-control"
                ]
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label(
                "image",
                t("Image"),
                [
                    "class" => "control-label"
                ]
            ); ?>

            <?php echo $fileSelector->image(
                "image",
                "image",
                t("Please select file"),
                $entry->getImage() instanceof File ? $entry->getImage()->getFileID() : null
            ); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("siteId", t("Site")); ?>
            <?php echo $form->select("siteId", $sites, $entry->getSite() instanceof \Concrete\Core\Entity\Site\Site ? $entry->getSite()->getSiteID() : null); ?>
        </div>


        <?php if (!empty($attributes)) {
            foreach ($attributes as $ak) {
                $renderer->buildView($ak)->render();
            }
        } ?>

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
                                <?php echo t("Name"); ?>
                            </th>

                            <th>
                                <?php echo t("Price"); ?>
                            </th>

                            <th>
                                <?php echo t("Quantity"); ?>
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
                                    <?php echo $variant->getName(); ?>
                                </td>

                                <td>
                                    <?php echo $variant->getPrice(); ?>
                                </td>

                                <td>
                                    <?php echo $variant->getQuantity(); ?>
                                </td>

                                <td>
                                    <div class="float-end">
                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/products/remove_variant", $variant->getId()); ?>" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo t('Remove'); ?>
                                        </a>

                                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/products/edit_variant", $variant->getId()); ?>" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-pencil" aria-hidden="true"></i> <?php echo t('Edit'); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <?php echo t("If you want to have price options you can do so by creating variants."); ?>
                <?php } ?>
            </fieldset>
        <?php } ?>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/bitter_shop_system/products"); ?>" class="btn btn-secondary">
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

<?php
