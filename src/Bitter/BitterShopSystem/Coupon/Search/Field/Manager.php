<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Coupon\Search\Field;

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\CodeField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\DiscountPercentageField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\DiscountPriceField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\ExcludeDiscountedProductsField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\LimitQuantityField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\MaximumDiscountAmountField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\MinimumOrderAmountField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\QuantityField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\TaxRateField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\UsePercentageDiscountField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\ValidFromField;
use Bitter\BitterShopSystem\Coupon\Search\Field\Field\ValidToField;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\Support\Facade\Application;
use Punic\Exception;
use Punic\Exception\BadArgumentType;
use DateTime;

class Manager extends FieldManager
{

    public function getTaxRate(Coupon $coupon): string
    {
        if ($coupon->getTaxRate() instanceof TaxRate) {
            return $coupon->getTaxRate()->getName();
        } else {
            return '';
        }
    }

    public function getDiscountPercentage(Coupon $coupon): string
    {
        return number_format($coupon->getDiscountPercentage()) . "%";
    }

    public function getUsePercentageDiscount(Coupon $coupon): string
    {
        return $coupon->isUsePercentageDiscount() ? t("Yes") : t("No");
    }

    public function getExcludeDiscountedProducts(Coupon $coupon): string
    {
        return $coupon->isExcludeDiscountedProducts() ? t("Yes") : t("No");
    }

    public function getLimitQuantity(Coupon $coupon): string
    {
        return $coupon->isLimitQuantity() ? t("Yes") : t("No");
    }

    public function getMinimumOrderAmount(Coupon $coupon): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($coupon->getMinimumOrderAmount());
    }

    public function getMaximumDiscountAmount(Coupon $coupon): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($coupon->getMaximumDiscountAmount());
    }

    public function getDiscountPrice(Coupon $coupon): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($coupon->getDiscountPrice());
    }

    public function getValidTo(Coupon $coupon): string
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);

        if ($coupon->getValidTo() instanceof DateTime) {
            try {
                return $dateService->formatDateTime($coupon->getValidTo());
            } catch (BadArgumentType | Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    public function getValidFrom(Coupon $coupon): string
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);

        if ($coupon->getValidFrom() instanceof DateTime) {
            try {
                return $dateService->formatDateTime($coupon->getValidFrom());
            } catch (BadArgumentType | Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    public function __construct()
    {
        $properties = [
            new CodeField(),
            new DiscountPercentageField(),
            new DiscountPriceField(),
            new ExcludeDiscountedProductsField(),
            new LimitQuantityField(),
            new MaximumDiscountAmountField(),
            new MinimumOrderAmountField(),
            new QuantityField(),
            new UsePercentageDiscountField(),
            new ValidFromField(),
            new ValidToField(),
            new TaxRateField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
