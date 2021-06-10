<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Coupon\Search\ColumnSet;

use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\DiscountPercentageColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\DiscountPriceColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\ExcludeDiscountedProductsColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\IdColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\LimitQuantityColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\MaximumDiscountAmountColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\MinimumOrderAmountColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\QuantityColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\TaxRateColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\UsePercentageDiscountColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\ValidFromColumn;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\ValidToColumn;

class Available extends DefaultSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new IdColumn());
        $this->addColumn(new DiscountPercentageColumn());
        $this->addColumn(new DiscountPriceColumn());
        $this->addColumn(new ExcludeDiscountedProductsColumn());
        $this->addColumn(new LimitQuantityColumn());
        $this->addColumn(new MaximumDiscountAmountColumn());
        $this->addColumn(new MinimumOrderAmountColumn());
        $this->addColumn(new QuantityColumn());
        $this->addColumn(new UsePercentageDiscountColumn());
        $this->addColumn(new ValidFromColumn());
        $this->addColumn(new ValidToColumn());
        $this->addColumn(new TaxRateColumn());
    }
}
