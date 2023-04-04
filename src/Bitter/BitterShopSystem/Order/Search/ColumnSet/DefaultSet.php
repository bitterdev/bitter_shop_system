<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\ColumnSet;

use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\OrderDateColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\SubtotalColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\TaxColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\TotalColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new OrderDateColumn());
        $this->addColumn(new SubtotalColumn());
        $this->addColumn(new TaxColumn());
        $this->addColumn(new TotalColumn());
        
        $id = $this->getColumnByKey('t4.orderDate');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
