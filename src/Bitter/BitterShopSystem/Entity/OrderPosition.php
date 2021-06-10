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

namespace Bitter\BitterShopSystem\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderPosition
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $price;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $tax;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @var Product
     * @OneToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Product")
     * @JoinColumn(name="productId", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Order", inversedBy="orderPositions")
     * @ORM\JoinColumn(name="orderId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $order;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return OrderPosition
     */
    public function setId(int $id): OrderPosition
    {
        $this->id = $id;
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
     * @return OrderPosition
     */
    public function setQuantity(int $quantity): OrderPosition
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return OrderPosition
     */
    public function setProduct(Product $product): OrderPosition
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return OrderPosition
     */
    public function setOrder(Order $order): OrderPosition
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return OrderPosition
     */
    public function setDescription(string $description): OrderPosition
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice($includeTax = false): float
    {
        return $includeTax ? $this->price + $this->tax : $this->price;
    }

    /**
     * @param float $price
     * @return OrderPosition
     */
    public function setPrice(float $price): OrderPosition
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     * @return OrderPosition
     */
    public function setTax(float $tax): OrderPosition
    {
        $this->tax = $tax;
        return $this;
    }

}