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

/** @var bool $enableGrid */
/** @var int $gridSize */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<section class="ccm-ui">
    <header>
        <h3>
            <?php echo t('General') ?>
        </h3>
    </header>

    <form action="<?php echo Url::to("/ccm/system/panels/pdf_editor/document/general/submit"); ?>" method="post"
          class="ccm-panel-detail-content-form" data-dialog-form="general" data-panel-detail-form="general"
          data-action-after-save="reload">

        <div class="form-check">
            <?php echo $form->checkbox("enableGrid", 1, $enableGrid); ?>
            <?php echo $form->label("enableGrid", t("Enable Grid"), ["class" => "form-check-label"]); ?>
        </div>

        <hr>

        <div class="form-group">
            <?php echo $form->label("gridSize", t("Grid Size")); ?>

            <div class="input-group">
                <?php echo $form->number("gridSize", $gridSize, ["min" => 0]); ?>

                <div class="input-group-text">
                    <?php echo t("mm"); ?>
                </div>
            </div>
        </div>

        <div class="ccm-panel-detail-form-actions dialog-buttons">
            <button class="float-start btn btn-secondary" type="button" data-dialog-action="cancel"
                    data-panel-detail-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button class="float-end btn btn-success" type="button" data-dialog-action="submit"
                    data-panel-detail-action="submit">
                <?php echo t('Save Changes') ?>
            </button>
        </div>
    </form>
</section>