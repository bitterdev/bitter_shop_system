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
use Bitter\BitterShopSystem\Entity\ProductVariant;
use Bitter\BitterShopSystem\Entity\TaxRate;
use JsonSerializable;

class CheckoutItem implements JsonSerializable
{
    /** @var Product */
    protected $product;
    /** @var ProductVariant|null */
    protected $productVariant;

    /** @var float */
    protected $quantity = 1;

    public function __construct(Product $product = null, int $quantity = 1, ?ProductVariant $productVariant = null)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->productVariant = $productVariant;
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
     * @return CheckoutItem
     */
    public function setProductVariant(?ProductVariant $productVariant): CheckoutItem
    {
        $this->productVariant = $productVariant;
        return $this;
    }

    public function getDisplayName(): string
    {
        if ($this->getProductVariant() instanceof ProductVariant) {
            return sprintf("%s - %s", $this->getProduct()->getName(), $this->getProductVariant()->getName());
        } else {
            return $this->getProduct()->getName();
        }
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
            if ($product->hasVariants() && $this->getProductVariant() instanceof ProductVariant) {
                return $this->getProductVariant()->getPrice($includeTax) * $this->getQuantity();
            } else {
                return $product->getPrice($includeTax) * $this->getQuantity();
            }
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
                if ($product->hasVariants() && $this->getProductVariant() instanceof ProductVariant) {
                    $price = $this->getProductVariant()->getPrice();
                } else {
                    $price = $product->getPrice();
                }

                return $price * $this->getQuantity() / 100 * $taxRate->getRate(true);
            }
        }

        return 0;
    }

    public function jsonSerialize()
    {
        return [
            "product" => $this->getProduct(),
            "productVariant" => $this->getProductVariant(),
            "quantity" => $this->getQuantity()
        ];
    }
}