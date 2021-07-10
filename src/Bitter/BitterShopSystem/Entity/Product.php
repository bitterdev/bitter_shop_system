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

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Value\ProductValue;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\Value as AttributeValue;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 */
class Product implements ObjectInterface, JsonSerializable, ExportableInterface
{
    use PackageTrait;
    use ObjectTrait;

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
    protected $name = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $handle = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $shortDescription = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $description = '';

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $quantity = 1;

    /**
     * @var File|null
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", onDelete="SET NULL")
     */
    protected $image = null;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $priceRegular = 0;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $priceDiscounted = 0;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\TaxRate", inversedBy="product")
     * @ORM\JoinColumn(name="taxRateId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxRate;

    /**
     * @var ShippingCost
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\ShippingCost", inversedBy="product")
     * @ORM\JoinColumn(name="shippingCostId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $shippingCost;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $locale;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Category", inversedBy="product")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $category;

    /**
     * @var \Bitter\BitterShopSystem\Entity\Attribute\Value\ProductValue[]
     * @ORM\OneToMany(targetEntity="\Bitter\BitterShopSystem\Entity\Attribute\Value\ProductValue", mappedBy="product", orphanRemoval=true)
     */
    protected $attributes;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function setId(int $id): Product
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
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     * @return Product
     */
    public function setHandle(string $handle): Product
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     * @return Product
     */
    public function setShortDescription(string $shortDescription): Product
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param bool $includeTax
     * @return float
     */
    public function getPriceRegular(bool $includeTax = false): ?float
    {
        return $includeTax ? $this->addTax($this->priceRegular) : $this->priceRegular;
    }

    /**
     * @param float $priceRegular
     * @return Product
     */
    public function setPriceRegular(float $priceRegular): Product
    {
        $this->priceRegular = $priceRegular;
        return $this;
    }

    /**
     * @param bool $includeTax
     * @return float
     */
    public function getPriceDiscounted(bool $includeTax = false): ?float
    {
        return $includeTax ? $this->addTax($this->priceDiscounted) : $this->priceDiscounted;
    }

    /**
     * @param float $priceDiscounted
     * @return Product
     */
    public function setPriceDiscounted(float $priceDiscounted): Product
    {
        $this->priceDiscounted = $priceDiscounted;
        return $this;
    }

    /**
     * @param bool $includeTax
     * @return float
     */
    public function getPrice(bool $includeTax = false): float
    {
        return $this->getPriceDiscounted($includeTax) > 0 ? $this->getPriceDiscounted($includeTax) : $this->getPriceRegular($includeTax);
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
     * @return TaxRate
     */
    public function getTaxRate(): ?TaxRate
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate|null|object $taxRate
     * @return Product
     */
    public function setTaxRate(TaxRate $taxRate): Product
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return ShippingCost
     */
    public function getShippingCost(): ?ShippingCost
    {
        return $this->shippingCost;
    }

    /**
     * @param ShippingCost|null|object $shippingCost
     * @return Product
     */
    public function setShippingCost(ShippingCost $shippingCost): Product
    {
        $this->shippingCost = $shippingCost;
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
     * @return Product
     */
    public function setQuantity(int $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @param File|null $image
     * @return Product
     */
    public function setImage(File $image): Product
    {
        $this->image = $image;
        return $this;
    }

    public function getObjectAttributeCategory(): ProductCategory
    {
        $app = Application::getFacadeApplication();
        return $app->make(ProductCategory::class);
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!($ak instanceof AttributeKeyInterface)) {
            $ak = $ak ? $this->getObjectAttributeCategory()->getAttributeKeyByHandle((string)$ak) : null;
        }

        if ($ak === null) {
            $result = null;
        } else {
            $result = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
            if ($result === null && $createIfNotExists) {
                $result = new ProductValue();
                $result->setProduct($this);
                $result->setAttributeKey($ak);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null|object $category
     * @return Product
     */
    public function setCategory(?Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param string $locale
     * @return Product
     */
    public function setLocale(string $locale): Product
    {
        $this->locale = $locale;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "handle" => $this->getHandle()
        ];
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\Product::class);;
    }
}
