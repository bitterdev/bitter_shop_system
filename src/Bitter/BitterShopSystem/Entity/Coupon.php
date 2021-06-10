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
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="`Coupon`")
 */
class Coupon implements ExportableInterface
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
     * @ORM\Column(type="text")
     */
    protected $code;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validFrom;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validTo;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $usePercentageDiscount = true;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $discountPrice;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $discountPercentage;

    /**
     * @var float|null
     * @ORM\Column(type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $maximumDiscountAmount;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $minimumOrderAmount;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $limitQuantity = false;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $excludeDiscountedProducts = false;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="Bitter\BitterShopSystem\Entity\TaxRate")
     * @ORM\JoinColumn(name="taxRateId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxRate;

    /**
     * @return TaxRate|null
     */
    public function getTaxRate(): ?TaxRate
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate|null|object $taxRate
     * @return Coupon
     */
    public function setTaxRate(?TaxRate $taxRate): Coupon
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Coupon
     */
    public function setId(int $id): Coupon
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return Coupon
     */
    public function setCode(?string $code): Coupon
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidFrom(): ?DateTime
    {
        return $this->validFrom;
    }

    /**
     * @param DateTime|null $validFrom
     * @return Coupon
     */
    public function setValidFrom(?DateTime $validFrom): Coupon
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidTo(): ?DateTime
    {
        return $this->validTo;
    }

    /**
     * @param DateTime|null $validTo
     * @return Coupon
     */
    public function setValidTo(?DateTime $validTo): Coupon
    {
        $this->validTo = $validTo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUsePercentageDiscount(): bool
    {
        return $this->usePercentageDiscount;
    }

    /**
     * @param bool $usePercentageDiscount
     * @return Coupon
     */
    public function setUsePercentageDiscount(bool $usePercentageDiscount): Coupon
    {
        $this->usePercentageDiscount = $usePercentageDiscount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param float $discountPrice
     * @return Coupon
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountPercentage()
    {
        return $this->discountPercentage;
    }

    /**
     * @param float $discountPercentage
     * @return Coupon
     */
    public function setDiscountPercentage($discountPercentage)
    {
        $this->discountPercentage = $discountPercentage;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaximumDiscountAmount(): ?float
    {
        return $this->maximumDiscountAmount;
    }

    /**
     * @param float|null $maximumDiscountAmount
     * @return Coupon
     */
    public function setMaximumDiscountAmount(?float $maximumDiscountAmount)
    {
        $this->maximumDiscountAmount = $maximumDiscountAmount;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinimumOrderAmount(): ?float
    {
        return $this->minimumOrderAmount;
    }

    /**
     * @param float|null $minimumOrderAmount
     * @return Coupon
     */
    public function setMinimumOrderAmount(?float $minimumOrderAmount)
    {
        $this->minimumOrderAmount = $minimumOrderAmount;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLimitQuantity(): bool
    {
        return $this->limitQuantity;
    }

    /**
     * @param bool $limitQuantity
     * @return Coupon
     */
    public function setLimitQuantity(bool $limitQuantity): Coupon
    {
        $this->limitQuantity = $limitQuantity;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     * @return Coupon
     */
    public function setQuantity(?int $quantity): Coupon
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExcludeDiscountedProducts(): bool
    {
        return $this->excludeDiscountedProducts;
    }

    /**
     * @param bool $excludeDiscountedProducts
     * @return Coupon
     */
    public function setExcludeDiscountedProducts(bool $excludeDiscountedProducts): Coupon
    {
        $this->excludeDiscountedProducts = $excludeDiscountedProducts;
        return $this;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\Coupon::class);;
    }

    public function validate(): ErrorList
    {
        $errorList = new ErrorList();
        $now = new DateTime();

        $app = Application::getFacadeApplication();
        /** @var CheckoutService $cartService */
        $cartService = $app->make(CheckoutService::class);

        if ($this->isLimitQuantity() && $this->getQuantity() < 1) {
            $errorList->add(t("There are no more coupons available with the given code."));
        }

        $totalToConsider = 0;

        foreach ($cartService->getAllItems() as $cartItem) {
            if ((
                    $this->isExcludeDiscountedProducts() &&
                    $cartItem->getProduct() instanceof Product &&
                    $cartItem->getProduct()->getPriceDiscounted() == 0
                ) ||
                !$this->isExcludeDiscountedProducts()
            ) {
                $totalToConsider += $cartItem->getSubtotal();
            }
        }

        if ($this->getMinimumOrderAmount() > 0 && $totalToConsider < $this->getMinimumOrderAmount()) {
            $errorList->add(t("You can't redeem the coupon code because you have not reached the minimum order amount."));
        }

        if ($this->getValidFrom() instanceof DateTime && $this->getValidFrom() > $now) {
            $errorList->add(t("You can't redeem the coupon code because it has not yet been activated."));
        }

        if ($this->getValidTo() instanceof DateTime && $now > $this->getValidTo()) {
            $errorList->add(t("You can't redeem the coupon code because it is expired."));
        }

        return $errorList;
    }
}