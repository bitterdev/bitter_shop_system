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

class CartItemAdded extends GenericEvent
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
     * @return CartItemAdded
     */
    public function setProduct(Product $product): CartItemAdded
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
     * @return CartItemAdded
     */
    public function setQuantity(int $quantity): CartItemAdded
    {
        $this->quantity = $quantity;
        return $this;
    }

}