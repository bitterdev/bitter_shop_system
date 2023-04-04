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

/** @var int $width */
/** @var int $height */
/** @var array $pageOrientations */
/** @var string $pageOrientation */


$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<section class="ccm-ui">
    <header>
        <?php echo t('Paper Size') ?>
    </header>

    <form action="<?php echo Url::to("/ccm/system/panels/pdf_editor/document/paper_size/submit"); ?>" method="post"
          class="ccm-panel-detail-content-form" data-dialog-form="paper-size" data-panel-detail-form="paper-size"
          data-action-after-save="reload">
        <div class="form-group">
            <?php echo $form->label("width", t("Width")); ?>

            <div class="input-group">
                <?php echo $form->number("width", $width, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("mm"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("height", t("Height")); ?>

            <div class="input-group">
                <?php echo $form->number("height", $height, ["min" => 0]); ?>

                <div class="input-group-addon">
                    <?php echo t("mm"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("pageOrientation", t("Page Orientation")); ?>
            <?php echo $form->select("pageOrientation", $pageOrientations, $pageOrientation); ?>
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