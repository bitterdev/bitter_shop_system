<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var int $firstPageId */
/** @var int $followingPageId */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);

?>

<section class="ccm-ui">
    <header>
        <h3>
            <?php echo t('Letterhead') ?>
        </h3>
    </header>

    <form action="<?php echo Url::to("/ccm/system/panels/pdf_editor/document/letterhead/submit"); ?>" method="post"
          class="ccm-panel-detail-content-form" data-dialog-form="letterhead" data-panel-detail-form="letterhead">
        <div class="form-group">
            <?php echo $form->label("firstPageId", t("First Page")); ?>
            <?php echo $fileManager->doc("firstPageId", "firstPageId", t("Please select file"), $firstPageId); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("followingPageId", t("Following Page")); ?>
            <?php echo $fileManager->doc("followingPageId", "followingPageId", t("Please select file"), $followingPageId); ?>
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