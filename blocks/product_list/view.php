<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Transformer\PriceTransformer;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\BitterShopSystem\Controller;

/** @var string $pagination */
/** @var int $detailsPageId */
/** @var array|Product[] $products */

$app = Application::getFacadeApplication();
/** @var PriceTransformer $priceTransformer */
$priceTransformer = $app->make(PriceTransformer::class);
/** @var PackageService $packageService */
$packageService = $app->make(PackageService::class);
$pkgEntity = $packageService->getByHandle("bitter_shop_system");
/** @var Controller $pkg */
$pkg = $pkgEntity->getController();

$detailsPage = Page::getByID($detailsPageId ?? null);
?>

<div class="product-list">
    <?php if (count($products) === 0) { ?>
        <p class="no-results">
            <?php echo t("There are no products available."); ?>
        </p>
    <?php } else { ?>
        <div class="row">
            <?php foreach ($products as $product) { ?>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <?php $productUrl = $detailsPage->isError() ? "javascript:void(0);" : (string)Url::to($detailsPage, "display_product", $product->getHandle()); ?>

                    <div class="product-list-item">
                        <?php
                        $imageUrl = $pkg->getRelativePath() . "/images/product_image_default.jpg";

                        if ($product->getImage() instanceof File) {
                            $approvedVersion = $product->getImage()->getApprovedVersion();

                            if ($approvedVersion instanceof Version) {
                                $imageUrl = $approvedVersion->getThumbnailURL("product_image");
                            }
                        }
                        ?>

                        <a href="<?php echo $productUrl; ?>">
                            <img src="<?php echo $imageUrl; ?>" alt="<?php echo h($product->getName()); ?>"
                                 class="img-responsive product-image"/>
                        </a>

                        <h2>
                            <a href="<?php echo $productUrl; ?>">
                                <?php echo $product->getName(); ?>
                            </a>
                        </h2>

                        <?php echo $priceTransformer->transform($product); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (isset($pagination) && $pagination) { ?>
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
    <?php } ?>
</div>