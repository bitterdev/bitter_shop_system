<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/** @var array $fontNames */
/** @var Block $block */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Color $colorPicker */
$colorPicker = $app->make(Color::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form action="<?php echo Url::to("/ccm/system/dialogs/pdf_editor/block_settings/submit", $block->getBlockTypeHandle(), $block->getId() ?? "add_new"); ?>"
      data-dialog-form="edit-block-settings"
      method="post"
      enctype="multipart/form-data">

    <?php echo $token->output("edit_block_settings"); ?>

    <div class="form-group">
        <?php echo $form->label("left", t("Left")); ?>

        <div class="input-group">
            <?php echo $form->number("left", $block->getLeft(), ["min" => 1]); ?>

            <div class="input-group-text">
                <?php echo t("mm"); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("top", t("Top")); ?>

        <div class="input-group">
            <?php echo $form->number("top", $block->getTop(), ["min" => 1]); ?>

            <div class="input-group-text">
                <?php echo t("mm"); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("width", t("Width")); ?>

        <div class="input-group">
            <?php echo $form->number("width", $block->getWidth(), ["min" => 1]); ?>

            <div class="input-group-text">
                <?php echo t("mm"); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("height", t("Height")); ?>

        <div class="input-group">
            <?php echo $form->number("height", $block->getHeight(), ["min" => 1]); ?>

            <div class="input-group-text">
                <?php echo t("mm"); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("fontName", t("Font Name")); ?>
        <?php echo $form->select("fontName", $fontNames, $block->getFontName()); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("fontSize", t("Font Size")); ?>

        <div class="input-group">
            <?php echo $form->number("fontSize", $block->getFontSize(), ["min" => 1]); ?>

            <div class="input-group-text">
                <?php echo t("px"); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("fontColor", t("Font Color")); ?>

        <div>
            <?php $colorPicker->output("fontColor", $block->getFontColor(), ['preferredFormat' => 'hex']); ?>
        </div>
    </div>

    <?php
    $configElement = $block->getBlockType()->getConfigurationElement();
    $configElement->setBlock($block);
    $configElement->render();
    ?>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-start" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
            <?php echo t('Save') ?>
        </button>
    </div>
</form>