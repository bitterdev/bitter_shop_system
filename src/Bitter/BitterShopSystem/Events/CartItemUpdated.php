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
use Symfony\Component\EventDispatcher\GenericEvent;

class CartItemUpdated extends GenericEvent
{
    /** @var Product */
    protected $product;
    /** @var int */
    protected $quantity;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return CartItemUpdated
     */
    public function setProduct(Product $product): CartItemUpdated
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return CartItemUpdated
     */
    public function setQuantity(int $quantity): CartItemUpdated
    {
        $this->quantity = $quantity;
        return $this;
    }
}