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
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var int|string $displayCaptcha */
/** @var int $termsOfUsePageId */
/** @var int $privacyPolicyPageId */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
?>

<div class="form-group">
    <?php echo $form->label("termsOfUsePageId", t("Terms of Use Page")); ?>
    <?php echo $pageSelector->selectPage("termsOfUsePageId", $termsOfUsePageId); ?>
</div>

<div class="form-group">
    <?php echo $form->label("privacyPolicyPageId", t("Privacy Policy Page")); ?>
    <?php echo $pageSelector->selectPage("privacyPolicyPageId", $privacyPolicyPageId); ?>
</div>

<div class="checkbox">
    <label for="displayCaptcha">
        <?php echo $form->checkbox("displayCaptcha", 1, (int)$displayCaptcha === 1); ?>

        <?php echo t("Display Captcha"); ?>
    </label>
</div>