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

use Bitter\BitterShopSystem\Entity\Category;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var $entry Category */
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
        <?php echo $token->output("save_category_entity"); ?>

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

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to("/dashboard/bitter_shop_system/products/categories"); ?>" class="btn btn-default">
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