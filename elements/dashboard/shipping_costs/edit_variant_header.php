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

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var ShippingCost $entry */
?>

<div class="btn-group">

    <?php \Concrete\Core\View\View::element("dashboard/help", [], "bitter_shop_system"); ?>

    <a href="<?php echo Url::to(Page::getCurrentPage(), "add_variant", $entry->getId()); ?>" class="btn btn-success">
        <?php echo t("Add Variant"); ?>
    </a>
</div>
