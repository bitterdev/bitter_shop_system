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

use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ShippingCost implements ExportableInterface
{
    use PackageTrait;

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
    protected $handle;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $price;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\TaxRate", inversedBy="shippingCosts")
     * @ORM\JoinColumn(name="taxRateId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxRate;

    /**
     * @var Collection|ShippingCostVariant[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\ShippingCostVariant", mappedBy="shippingCost", cascade={"persist", "remove"})
     */
    protected $variants;

    /**
     * @var \Bitter\BitterShopSystem\Entity\Product[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\Product", mappedBy="shippingCost", orphanRemoval=true)
     */
    protected $products;

    /**
     * @return TaxRate
     */
    public function getTaxRate(): ?TaxRate
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate|null|object $taxRate
     * @return ShippingCost
     */
    public function setTaxRate(?TaxRate $taxRate): ShippingCost
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return ShippingCost
     */
    public function setId($id): ShippingCost
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ShippingCost
     */
    public function setName(string $name): ShippingCost
    {
        $this->name = $name;
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
        } else if ($this->getTaxRate() instanceof TaxRate) {
            return $amount / 100 * (100 + $this->getTaxRate()->getRate());
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
     * @param float $price
     * @return ShippingCost
     */
    public function setPrice(float $price): ShippingCost
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string|null $handle
     * @return ShippingCost
     */
    public function setHandle($handle): ShippingCost
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * @return ShippingCostVariant[]|Collection
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param ShippingCostVariant[]|Collection $variants
     * @return ShippingCost
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
        return $this;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\ShippingCost::class);;
    }

}