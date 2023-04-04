<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?php echo Url::to(Page::getCurrentPage(), "preview"); ?>" class="btn btn-default" target="_blank">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo t("Preview"); ?>
    </a>
</div>