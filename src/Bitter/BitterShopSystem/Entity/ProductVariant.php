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
use JetBrains\PhpStorm\Internal\TentativeType;

/**
 * @ORM\Entity
 */
class ProductVariant implements \JsonSerializable
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\Product", inversedBy="variants")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $product;

    /**
     * @var float|null
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $price = 0;

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    protected $name = '';

    /**
     * @var int|null
     * @ORM\Column(type="integer")
     */
    protected $quantity = 1;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @param float|null $price
     */
    public function setPrice(float|int|null $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param float|null $amount
     * @return float|null
     */
    private function addTax(?float $amount): ?float
    {
        if ($amount === null) {
            return 0;
        } else if ($this->getProduct()->getTaxRate() instanceof TaxRate) {
            return $amount / 100 * (100 + $this->getProduct()->getTaxRate()->getRate());
        } else {
            return $amount;
        }
    }

    /**
     * @param bool $includeTax
     * @return float
     */
    public function getPrice(bool $includeTax = false): ?float
    {
        return $includeTax ? $this->addTax($this->price) : $this->price;
    }

    /**
     * @param int|null $quantity
     */
    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId()
        ];
    }
}