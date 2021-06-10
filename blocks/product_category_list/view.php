<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\Category;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var int $productListPageId */
/** @var array|Category[] $productCategories */

$productListPage = Page::getByID($productListPageId);
?>

<div class="product-category-list">
    <?php if (count($productCategories) === 0) { ?>
        <p class="no-results">
            <?php echo t("There are no product categories available."); ?>
        </p>
    <?php } else { ?>
    <nav>
        <ul>
            <?php foreach ($productCategories as $productCategory) { ?>
                <li>
                    <a href="<?php echo Url::to($productListPage, "filter_by_category", $productCategory->getHandle()); ?>">
                        <?php echo $productCategory->getName(); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </nav>
</div>