<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Block\ProductDetails;

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\User;

class Controller extends BlockController
{
    protected $btTable = "btProductDetails";
    protected $btExportPageColumns = ['cartPageId'];

    public function getBlockTypeDescription(): string
    {
        return t('Add product details.');
    }

    public function getBlockTypeName(): string
    {
        return t('Product Details');
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset("javascript", "jquery");
        $this->requireAsset("core/app");
    }

    public function action_display_product($handle = '')
    {
        /** @var Seo $seoService */
        $seoService = $this->app->make('helper/seo');
        /** @var ProductService $productService */
        $productService = $this->app->make(ProductService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $product = $productService->getByHandleWithCurrentLocale($handle);
        if ($product instanceof Product) {
            $seoService->setCustomTitle($product->getName());
            $this->set('product', $product);
        } else {
            return $responseFactory->notFound(t("Product not found."));
        }
    }

    public function view()
    {
        $user = new User();
        if ($user->isSuperUser()) {
            $product = new Product();
            $product->setName(t("Sample Product"));
            $this->set('product', $product);
        } else {
            /** @var ResponseFactory $responseFactory */
            $responseFactory = $this->app->make(ResponseFactory::class);
            $responseFactory->notFound(t("Product not found."))->send();
            $this->app->shutdown();
        }
    }

    public function add()
    {
        $this->set("cartPageId", null);
    }
}
