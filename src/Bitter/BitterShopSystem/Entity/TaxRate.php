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

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TaxRate implements ExportableInterface
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
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $handle;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $rate;

    /**
     * @var Collection|TaxRateVariant[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\TaxRateVariant", mappedBy="taxRate", cascade={"persist", "remove"})
     */
    protected $variants;

    /**
     * @var \Bitter\BitterShopSystem\Entity\ShippingCost[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\ShippingCost", mappedBy="taxRate", orphanRemoval=true)
     */
    protected $shippingCosts;

    /**
     * @var \Bitter\BitterShopSystem\Entity\Product[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\Product", mappedBy="taxRate", orphanRemoval=true)
     */
    protected $products;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return TaxRate
     */
    public function setId($id): TaxRate
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
     * @return TaxRate
     */
    public function setName(string $name): TaxRate
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getRate($detectVariantByBillingAddress = false): ?float
    {
        if ($detectVariantByBillingAddress && count($this->getVariants()) > 0) {
            $app = Application::getFacadeApplication();
            /** @var CheckoutService $cartService */
            $cartService = $app->make(CheckoutService::class);

            $billingAddress = $cartService->getCustomerAttribute("billing_address");

            if ($billingAddress instanceof AddressValue) {
                $customerCountry = (string)$billingAddress->getCountry();
                $customerState = (string)$billingAddress->getStateProvince();

                foreach ($this->getVariants() as $taxRateVariant) {
                    if ($customerCountry === $taxRateVariant->getCountry() &&
                        ($taxRateVariant->getState() === "" || $customerState === $taxRateVariant->getState())) {

                        return $taxRateVariant->getRate();
                    }
                }
            }
        }

        return $this->rate;
    }

    /**
     * @param float $rate
     * @return TaxRate
     */
    public function setRate(float $rate): TaxRate
    {
        $this->rate = $rate;
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
     * @param string|null $handle
     * @return TaxRate
     */
    public function setHandle($handle): TaxRate
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * @return TaxRateVariant[]|Collection
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param TaxRateVariant[]|Collection $variants
     * @return TaxRate
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
        return $this;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\TaxRate::class);;
    }
}