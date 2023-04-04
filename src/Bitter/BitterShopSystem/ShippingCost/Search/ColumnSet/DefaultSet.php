<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\ShippingCost\Search\ColumnSet;

use Bitter\BitterShopSystem\ShippingCost\Search\ColumnSet\Column\NameColumn;
use Bitter\BitterShopSystem\ShippingCost\Search\ColumnSet\Column\PriceColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new PriceColumn());
        
        $id = $this->getColumnByKey('t1.name');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
