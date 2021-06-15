<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Support\Facade\Url;

?>

<section>
    <header>
        <?php echo t("Document Settings") ?>
    </header>

    <menu class="ccm-panel-page-basics">
        <li>
            <a href="#" data-launch-panel-detail="pdf-document-general"
               data-panel-detail-url="<?php echo Url::to('/ccm/system/panels/pdf_editor/document/general') ?>">
                <?php echo t("General") ?>
            </a>
        </li>

        <li>
            <a href="#" data-launch-panel-detail="pdf-document-paper-size"
               data-panel-detail-url="<?php echo Url::to('/ccm/system/panels/pdf_editor/document/paper_size') ?>">
                <?php echo t("Paper Size") ?>
            </a>
        </li>

        <li>
            <a href="#" data-launch-panel-detail="pdf-document-margins"
               data-panel-detail-url="<?php echo Url::to('/ccm/system/panels/pdf_editor/document/margins') ?>">
                <?php echo t("Margins") ?>
            </a>
        </li>

        <li>
            <a href="#" data-launch-panel-detail="pdf-letterhead"
               data-panel-detail-url="<?php echo Url::to('/ccm/system/panels/pdf_editor/document/letterhead') ?>">
                <?php echo t("Letterhead") ?>
            </a>
        </li>
    </menu>
</section>