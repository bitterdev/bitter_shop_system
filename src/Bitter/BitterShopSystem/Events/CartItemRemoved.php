<?php /** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Events;

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ProductVariant;
use Symfony\Component\EventDispatcher\GenericEvent;

class CartItemRemoved extends GenericEvent
{
    /** @var Product */
    protected $product;
    /** @var ProductVariant|null */
    protected $productVariant;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return CartItemRemoved
     */
    public function setProduct(Product $product): CartItemRemoved
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return ProductVariant|null
     */
    public function getProductVariant(): ?ProductVariant
    {
        return $this->productVariant;
    }

    /**
     * @param ProductVariant|null $productVariant
     * @return CartItemRemoved
     */
    public function setProductVariant(?ProductVariant $productVariant): CartItemRemoved
    {
        $this->productVariant = $productVariant;
        return $this;
    }

}