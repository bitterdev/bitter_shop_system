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

use Bitter\BitterShopSystem\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\View\View;

/** @var $entry Customer */
/** @var $form Form */
/** @var $token Token */
/** @var Renderer $renderer */
/** @var CustomerKey[] $attributes */

$app = Application::getFacadeApplication();
/** @var UserSelector $userSelector */
$userSelector = $app->make(UserSelector::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "bitter_shop_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/bitter-shop-system/reviews"], 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');

?>

<form action="#" method="post">
    <?php echo $token->output("save_customer_entity"); ?>

    <div class="form-group">
        <?php echo $form->label(
            "email",
            t("Email"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <span class="text-muted small">
            <?php echo t('Required') ?>
        </span>

        <?php echo $form->email(
            "email",
            $entry->getEmail(),
            [
                "class" => "form-control",
                "max-length" => "255",
            ]
        ); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label(
            "user",
            t("User"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <?php echo $userSelector->selectUser(
            "user",
            $entry->getUser() instanceof User ? $entry->getUser()->getUserID() : null
        ); ?>
    </div>

    <?php if (!empty($attributes)) {
        foreach ($attributes as $ak) {
            $renderer->buildView($ak)->render();
        }
    } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/bitter_shop_system/customers"); ?>" class="btn btn-default">
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