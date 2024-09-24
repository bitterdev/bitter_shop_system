<?php /** @noinspection PhpInconsistentReturnPointsInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Block\Cart;

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ProductVariant;
use Bitter\BitterShopSystem\Product\ProductService;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Exception;

class Controller extends BlockController
{
    protected $btTable = "btCart";
    protected $error;
    /** @var ProductService */
    protected $productService;
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var CheckoutService */
    protected $cartService;
    protected $btExportPageColumns = ['checkoutPageId'];

    public function getBlockTypeDescription(): string
    {
        return t('Add shopping cart.');
    }

    public function getBlockTypeName(): string
    {
        return t('Shopping Cart');
    }

    public function action_added()
    {
        $this->set('success', t("The product has been successfully added."));
    }

    public function action_updated()
    {
        $this->set('success', t("The product has been successfully updated."));
    }

    public function action_removed()
    {
        $this->set('success', t("The product has been successfully removed."));
    }

    public function on_start()
    {
        parent::on_start();
        $this->error = new ErrorList();
        $this->productService = $this->app->make(ProductService::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->cartService = $this->app->make(CheckoutService::class);

        // reset the cached checkout page the prevent issues
        $this->cartService->setCheckoutPageId(0);
    }

    public function on_before_render()
    {
        parent::on_before_render();
        $this->set("error", $this->error);
    }

    public function action_remove($productHandle = '', $productVariantId = null)
    {
        $product = $this->productService->getByHandleWithCurrentLocale($productHandle);
        if ($product instanceof Product) {
            if ($product->hasVariants()) {
                $productVariant = $product->getVariantById($productVariantId);

                if ($productVariant instanceof ProductVariant) {
                    try {
                        $this->cartService->removeItem($product, $productVariant);
                        return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'removed'), Response::HTTP_TEMPORARY_REDIRECT);
                    } catch (Exception $e) {
                        $this->error->add($e);
                    }
                } else {
                    return $this->responseFactory->notFound(t("Product variation not found."));
                }
            } else {
                try {
                    $this->cartService->removeItem($product);
                    return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'removed'), Response::HTTP_TEMPORARY_REDIRECT);
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            }
        } else {
            return $this->responseFactory->notFound(t("Product not found."));
        }
    }

    public function action_update($productHandle = '', $productVariantId = null)
    {
        $product = $this->productService->getByHandleWithCurrentLocale($productHandle);

        $quantity = (int)$this->request->query->get("quantity", 1);

        if ($product instanceof Product) {

            if ($product->hasVariants()) {
                $productVariant = $product->getVariantById($productVariantId);

                if ($productVariant instanceof ProductVariant) {
                    try {
                        $this->cartService->updateItem($product, $quantity, $productVariant);
                        return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'updated'), Response::HTTP_TEMPORARY_REDIRECT);
                    } catch (Exception $e) {
                        $this->error->add($e);
                    }
                } else {
                    return $this->responseFactory->notFound(t("Product variation not found."));
                }
            } else {
                try {
                    $this->cartService->updateItem($product, $quantity);
                    return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'updated'), Response::HTTP_TEMPORARY_REDIRECT);
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            }
        } else {
            return $this->responseFactory->notFound(t("Product not found."));
        }
    }

    public function action_add($productHandle = '', $productVariantId = null)
    {
        $product = $this->productService->getByHandleWithCurrentLocale($productHandle);

        $quantity = (int)$this->request->query->get("quantity", 1);

        if ($product instanceof Product) {
            if ($product->hasVariants()) {
                $productVariant = $product->getVariantById($productVariantId);

                if ($productVariant instanceof ProductVariant) {
                    try {
                        $this->cartService->addItem($product, $quantity, $productVariant);
                        return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'added'), Response::HTTP_TEMPORARY_REDIRECT);
                    } catch (Exception $e) {
                        $this->error->add($e);
                    }
                } else {
                    return $this->responseFactory->notFound(t("Product variation not found."));
                }
            } else {
                try {
                    $this->cartService->addItem($product, $quantity);
                    return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), 'added'), Response::HTTP_TEMPORARY_REDIRECT);
                } catch (Exception $e) {
                    $this->error->add($e);
                }
            }
        } else {
            return $this->responseFactory->notFound(t("Product not found."));
        }
    }

    public function add()
    {
        $this->set("checkoutPageId", null);
    }
}
