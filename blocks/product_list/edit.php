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

/** @var int $detailsPageId */
/** @var int $itemsPerPage */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
?>

<div class="form-group">
    <?php echo $form->label("detailsPageId", t("Details Page")); ?>
    <?php echo $pageSelector->selectPage("detailsPageId", $detailsPageId); ?>
</div>

<div class="form-group">
    <?php echo $form->label("itemsPerPage", t("Items per Page")); ?>
    <?php echo $form->number("itemsPerPage", $itemsPerPage, ["min" => 1]); ?>
</div>