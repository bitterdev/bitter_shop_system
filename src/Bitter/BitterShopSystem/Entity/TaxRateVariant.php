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
class TaxRateVariant
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\TaxRate", inversedBy="variants")
     * @ORM\JoinColumn(name="taxRateId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxRate;

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
    protected $rate = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TaxRateVariant
     */
    public function setId(int $id): TaxRateVariant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return TaxRate
     */
    public function getTaxRate(): TaxRate
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate $taxRate
     * @return TaxRateVariant
     */
    public function setTaxRate(TaxRate $taxRate): TaxRateVariant
    {
        $this->taxRate = $taxRate;
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
     * @return TaxRateVariant
     */
    public function setCountry(string $country): TaxRateVariant
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
     * @return TaxRateVariant
     */
    public function setState(string $state): TaxRateVariant
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     * @return TaxRateVariant
     */
    public function setRate(float $rate): TaxRateVariant
    {
        $this->rate = $rate;
        return $this;
    }

}