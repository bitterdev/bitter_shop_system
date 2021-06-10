<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Checkout;

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\TaxRate;
use JsonSerializable;

class CheckoutItem implements JsonSerializable
{
    /** @var Product */
    protected $product;

    /** @var float */
    protected $quantity = 1;

    public function __construct(Product $product = null, int $quantity = 1)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return CheckoutItem
     */
    public function setProduct(Product $product): CheckoutItem
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     * @return CheckoutItem
     */
    public function setQuantity(float $quantity): CheckoutItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getSubtotal($includeTax = false): float
    {
        $product = $this->getProduct();

        if ($product instanceof Product) {
            return $product->getPrice($includeTax) * $this->getQuantity();
        }

        return 0;
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getTax();
    }

    public function getTax(): float
    {
        $product = $this->getProduct();

        if ($product instanceof Product) {
            $taxRate = $product->getTaxRate();

            if ($taxRate instanceof TaxRate) {
                return $product->getPrice() * $this->getQuantity() / 100 * $taxRate->getRate(true);
            }
        }

        return 0;
    }

    public function jsonSerialize()
    {
        return [
            "product" => $this->getProduct(),
            "quantity" => $this->getQuantity()
        ];
    }
}