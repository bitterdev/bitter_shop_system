<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var array $locales */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "bitter_shop_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/bitter-shop-system/reviews"], 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');

?>

<?php if (count($locales) === 1) { ?>
    <p>
        <?php echo t("You must have more than one locale to use this tool.") ?>
    </p>
<?php } else { ?>
    <form action="#" method="post">
        <?php echo $token->output("copy_products"); ?>

        <p>
            <?php echo t('Copy all products from a locale to another locale. This will only copy products that not exists with the target locale. It will not replace or remove any products.') ?>
        </p>

        <div class="form-group">
            <?php echo $form->label("sourceLocale", t("Copy From"), ["class" => "control-label"]); ?>
            <?php echo $form->select("sourceLocale", $locales, ["class" => "form-control", "required" => "required"]); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("targetLocale", t("Copy To"), ["class" => "control-label"]); ?>
            <?php echo $form->select("targetLocale", $locales, ["class" => "form-control", "required" => "required"]); ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
                    </button>
                </div>
            </div>
        </div>

    </form>
<?php } ?>

<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');