<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\Value\MultipleFilesValue;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ProductVariant;
use Bitter\BitterShopSystem\Transformer\PriceTransformer;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\BitterShopSystem\Controller;

/** @var Product|null $product */
/** @var ProductVariant|null $productVariant */
/** @var int $cartPageId */

$c = Page::getCurrentPage();
$app = Application::getFacadeApplication();
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);
/** @var Form $form */
$form = $app->make(Form::class);
$cartPage = Page::getByID($cartPageId);
/** @var PriceTransformer $priceTransformer */
$priceTransformer = $app->make(PriceTransformer::class);
/** @var CategoryService $service */
$service = $app->make(CategoryService::class);
$categoryEntity = $service->getByHandle('product');
/** @var ProductCategory $category */
$category = $categoryEntity->getController();
$setManager = $category->getSetManager();
/** @var PackageService $packageService */
$packageService = $app->make(PackageService::class);
$pkgEntity = $packageService->getByHandle("bitter_shop_system");
/** @var Controller $pkg */
$pkg = $pkgEntity->getController();

$productVariant = $productVariant ?? null;
?>

<?php
$detailImages = $product->getAttribute("detail_images");

$quantityValues = [];
$maxQuantity = (int)$config->get("bitter_shop_system.max_quantity", $product->getQuantity());
if ($productVariant instanceof ProductVariant) {
    $maxQuantity = (int)$config->get("bitter_shop_system.max_quantity", $productVariant->getQuantity());
}
for ($i = 1; $i <= $maxQuantity; $i++) {
    $quantityValues[$i] = $i;
}
?>
<div class="product-details">
    <div class="row">
        <div class="product-header row">
            <div class="col-md-6">
                <?php
                $imageUrl = $pkg->getRelativePath() . "/images/product_image_default.jpg";

                if ($product->getImage() instanceof File) {
                    $approvedVersion = $product->getImage()->getApprovedVersion();

                    if ($approvedVersion instanceof Version) {
                        $imageUrl = $approvedVersion->getThumbnailURL("product_image");
                    }
                }
                ?>

                <img src="<?php echo $imageUrl; ?>" alt="<?php echo h($product->getName()); ?>"
                     id="ccm-large-image"
                     class="img-responsive"/>

                <?php if ($detailImages instanceof MultipleFilesValue) { ?>
                    <div class="detail-images">
                        <?php foreach ($detailImages->getFileObjects() as $file) { ?>
                            <?php if ($file instanceof File) { ?>
                                <?php $approvedVersion = $file->getApprovedVersion(); ?>

                                <?php if ($approvedVersion instanceof Version) { ?>
                                    <a href="javascript:void(0);"
                                       data-large-image-url="<?php echo $approvedVersion->getThumbnailURL("product_image"); ?>"
                                       class="detail-image"
                                       title="<?php echo h(t("Click to enlarge")); ?>">

                                        <img src="<?php echo $approvedVersion->getThumbnailURL("product_thumbnail"); ?>"
                                             alt="<?php echo h($product->getName()); ?>"/>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?></div>

                <?php } ?>
            </div>

            <div class="col-md-6">
                <h1>
                    <?php echo $product->getName(); ?>
                </h1>

                <p>
                    <?php echo strlen($product->getShortDescription()) > 0 ? $product->getShortDescription() : t("No short description available."); ?>
                </p>

                <?php echo $priceTransformer->transform($product, $productVariant); ?>

                <?php if (!$cartPage->isError() && $product->getQuantity() > 0) { ?>
                    <form action="<?php echo $productVariant instanceof ProductVariant ? Url::to($cartPage, "add", $product->getHandle(), $productVariant->getId()) : Url::to($cartPage, "add", $product->getHandle()); ?>"
                          method="get">

                        <?php if ($product->hasVariants()) { ?>
                            <div class="form-group">
                                <?php echo $form->label("variant", t("Variant")); ?>
                                <?php echo $form->select(
                                    "variant",
                                    $product->getVariantList(), $productVariant instanceof ProductVariant ? $productVariant->getId() : null,
                                    [
                                        "data-base-url" => Url::to(Page::getCurrentPage(), "display_product", $product->getHandle())
                                    ]
                                ); ?>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <?php echo $form->label("quantity", t("Quantity")); ?>
                            <?php echo $form->select("quantity", $quantityValues, 1); ?>
                        </div>

                        <button type="submit"
                                class="btn btn-primary">
                            <?php echo t("Add to Cart"); ?>
                        </button>
                    </form>
                <?php } else { ?>
                    <div class="form-group">
                        <?php echo $form->label("quantity", t("Quantity")); ?>
                        <?php echo $form->select("quantity", [0 => t("Out of stock")], 0, ["disabled" => "disabled"]); ?>
                    </div>

                    <a href="javascript:void(0);" class="btn btn-primary disabled">
                        <?php echo t("Add to Cart"); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="product-body">
        <?php /** @noinspection PhpParamsInspection */
        echo $userInterface->tabs([
            ['general', t("General"), true],
            ['details', t("Details")]
        ]); ?>

        <div class="tab-content">
            <div class="tab-pane active" id="general" role="tabpanel">
                <?php echo strlen($product->getDescription()) > 0 ? $product->getDescription() : t("No description available."); ?>
            </div>

            <div class="tab-pane" id="details" role="tabpanel">
                <?php
                $additionalDetails = [];

                foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                    /** @var ProductKey $ak */
                    $attributeValue = (string)$product->getAttributeValue($ak);
                    if ($ak->getAttributeKeyHandle() !== "detail_images" && strlen($attributeValue) > 0) {
                        $additionalDetails[$ak->getAttributeKeyName()] = $attributeValue;
                    }
                }
                ?>

                <?php if (count($additionalDetails) === 0) { ?>
                    <p>
                        <?php echo t("No details available."); ?>
                    </p>
                <?php } else { ?>
                    <table class="table table-striped">
                        <tbody>
                        <?php foreach ($additionalDetails as $attributeName => $attributeValue) { ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?php echo $attributeName; ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo $attributeValue; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
