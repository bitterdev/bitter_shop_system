<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\View\View;


?>


<div class="ccm-dashboard-header-buttons">
    <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>
</div>

<?php

/** @var KeyList $attributeView */
$attributeView->render();

