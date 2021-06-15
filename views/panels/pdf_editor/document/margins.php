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
use Concrete\Core\Support\Facade\Url;

/** @var int $top */
/** @var int $left */
/** @var int $bottom */
/** @var int $right */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<section class="ccm-ui">
    <header>
        <?php echo t('Margins') ?>
    </header>

    <form action="<?php echo Url::to("/ccm/system/panels/pdf_editor/document/margins/submit"); ?>" method="post"
          class="ccm-panel-detail-content-form" data-dialog-form="margins" data-panel-detail-form="margins">
        <div class="form-group">
            <?php echo $form->label("top", t("Top")); ?>

            <div class="input-group">
                <?php echo $form->number("top", $top, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("bottom", t("Bottom")); ?>

            <div class="input-group">
                <?php echo $form->number("bottom", $bottom, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("left", t("Left")); ?>

            <div class="input-group">
                <?php echo $form->number("left", $left, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("right", t("Right")); ?>

            <div class="input-group">
                <?php echo $form->number("right", $right, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("px"); ?>
                </div>
            </div>
        </div>

        <div class="ccm-panel-detail-form-actions dialog-buttons">
            <button class="pull-left btn btn-default" type="button" data-dialog-action="cancel"
                    data-panel-detail-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="pull-right btn btn-success" type="button" data-dialog-action="submit"
                    data-panel-detail-action="submit">
                <?php echo t('Save Changes') ?>
            </button>
        </div>
    </form>
</section>