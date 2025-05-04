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

use Bitter\BitterShopSystem\Entity\ProductVariant;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Config\Repository\Repository;

/** @var ProductVariant $variant */
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

/** @var Repository $config */
$config = $app->make(Repository::class);


?>
    <div class="ccm-dashboard-header-buttons">
        <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>
    </div>

    <?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "bitter_shop_system"); ?>

<form action="#" method="post">
    <?php echo $token->output("save_product_variant"); ?>

    <div class="form-group">
        <?php echo $form->label(
            "name",
            t("Name"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <?php echo $form->text(
            "name",
            $variant->getName(),
            [
                "max-length" => 255,
                "class" => "form-control"
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

        <div class="input-group">
            <?php echo $form->number(
                "price",
                $variant->getPrice(),
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
            "quantity",
            t("Quantity"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <?php echo $form->number(
            "quantity",
            $variant->getQuantity(),
            [
                "min" => 0,
                "class" => "form-control"
            ]
        ); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/bitter_shop_system/products/edit", $variant->getProduct()->getId()); ?>" class="btn btn-secondary">
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
