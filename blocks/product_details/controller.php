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

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ProductVariant;
use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Exception;

class Controller extends BlockController
{
    protected $btTable = "btProductDetails";
    protected $btExportPageColumns = ['cartPageId'];
    protected $error;

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
        $this->requireAsset("javascript", "bootstrap");
        $this->requireAsset("core/app");
    }

    public function on_start()
    {
        parent::on_start();
        $this->error = new ErrorList();
    }

    public function action_add($productHandle = '', $productVariantId = null)
    {
        /** @var ProductService $productService */
        $productService = $this->app->make(ProductService::class);
        /** @var CheckoutService $cartService */
        $cartService = $this->app->make(CheckoutService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $product = $productService->getByHandleWithCurrentLocale($productHandle);

        $quantity = (int)$this->request->query->get("quantity", 1);

        if ($product instanceof Product) {
            if ($product->hasVariants()) {
                $productVariant = $product->getVariantById($productVariantId);

                if ($productVariant instanceof ProductVariant) {
                    try {
                        $cartService->addItem($product, $quantity, $productVariant);
                        return $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'display_product', $productHandle, $productVariantId)->setQuery(["added" => true]), Response::HTTP_TEMPORARY_REDIRECT);
                    } catch (Exception $e) {
                        $this->error->add($e);
                    }
                } else {
                    return $responseFactory->notFound(t("Product variation not found."));
                }
            } else {
                try {
                    $cartService->addItem($product, $quantity);
                    return $responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'display_product', $productHandle, $productVariantId)->setQuery(["added" => true]), Response::HTTP_TEMPORARY_REDIRECT);
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            }
        } else {
            return $responseFactory->notFound(t("Product not found."));
        }
    }

    public function action_display_product($handle = '', $variant = null)
    {
        /** @var Seo $seoService */
        $seoService = $this->app->make('helper/seo');
        /** @var ProductService $productService */
        $productService = $this->app->make(ProductService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $product = $productService->getByHandleWithCurrentLocale($handle);

        if ($this->request->query->has("added")) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->set('success', t("The product has been successfully added. Click %s to proceed checkout.", sprintf(
                "<a href='%s'>%s</a>",
                h(Url::to(Page::getByID($this->cartPageId))),
                t("here")
            )));
        }

        if ($product instanceof Product) {
            $seoService->setCustomTitle($product->getName());
            $this->set('product', $product);

            if ($product->hasVariants()) {
                if (isset($variant) && is_numeric($variant)) {
                    $productVariant = $product->getVariantById((int)$variant);

                    if ($productVariant instanceof ProductVariant) {
                        $this->set('productVariant', $productVariant);
                    } else {
                        return $responseFactory->notFound(t("Product variation not found."));
                    }
                } else {
                    $this->set('productVariant', $product->getVariants()->first());
                }
            }
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
