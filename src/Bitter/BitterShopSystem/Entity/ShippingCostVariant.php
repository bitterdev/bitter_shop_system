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
class ShippingCostVariant
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ShippingCost
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\ShippingCost", inversedBy="variants")
     * @ORM\JoinColumn(name="shippingCostId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $shippingCost;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $country = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $state = '';

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $price = 0;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ShippingCostVariant
     */
    public function setId(int $id): ShippingCostVariant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ShippingCost
     */
    public function getShippingCost(): ShippingCost
    {
        return $this->shippingCost;
    }

    /**
     * @param ShippingCost $shippingCost
     * @return ShippingCostVariant
     */
    public function setShippingCost(ShippingCost $shippingCost): ShippingCostVariant
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return ShippingCostVariant
     */
    public function setCountry(string $country): ShippingCostVariant
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return ShippingCostVariant
     */
    public function setState(string $state): ShippingCostVariant
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param float|null $amount
     * @return float|null
     */
    private function addTax(?float $amount): ?float
    {
        if ($amount === null) {
            return 0;
        } else if ($this->getShippingCost()->getTaxRate() instanceof TaxRate) {
            return $amount / 100 * (100 + $this->getShippingCost()->getTaxRate()->getRate());
        } else {
            return $amount;
        }
    }

    /**
     * @return float
     */
    public function getPrice(bool $includeTax = false): ?float
    {
        return $includeTax ? $this->addTax($this->price) : $this->price;
    }

    /**
     * @param float $price
     * @return ShippingCostVariant
     */
    public function setPrice(float $price): ShippingCostVariant
    {
        $this->price = $price;
        return $this;
    }

}