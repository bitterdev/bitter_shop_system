<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\ColumnSet;

use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\LocaleColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\NameColumn;
use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\PriceRegularColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\PriceDiscountedColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\TaxRateColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ShippingCostColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = ProductKey::class;
    
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new PriceRegularColumn());
        $this->addColumn(new PriceDiscountedColumn());
        $this->addColumn(new TaxRateColumn());
        $this->addColumn(new ShippingCostColumn());
        $this->addColumn(new LocaleColumn());
        
        $id = $this->getColumnByKey('t2.name');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
