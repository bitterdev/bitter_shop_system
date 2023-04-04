<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\TaxRate\Search\ColumnSet;

use Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Column\NameColumn;
use Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Column\RateColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new RateColumn());
        
        $id = $this->getColumnByKey('t0.name');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
