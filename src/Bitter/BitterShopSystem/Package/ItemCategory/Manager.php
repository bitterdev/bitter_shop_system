<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Package\ItemCategory;

use Concrete\Core\Package\ItemCategory\Manager as CoreManager;

class Manager extends CoreManager
{
    public function createCouponDriver(): Product
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(Coupon::class);
    }

    public function createProductDriver(): Product
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(Product::class);
    }

    public function createShippingCostDriver(): ShippingCost
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(ShippingCost::class);
    }

    public function createTaxRateDriver(): TaxRate
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(TaxRate::class);
    }

    public function createCustomerDriver(): Customer
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(Customer::class);
    }

    public function createOrderDriver(): Order
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(Order::class);
    }

    public function createCategoryDriver(): Order
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(Category::class);
    }

    public function getPackageItemCategories()
    {
        return parent::getPackageItemCategories() + ["product", "shipping_cost", "tax_rate", "customer", "order", "coupon", "category"];
    }
}