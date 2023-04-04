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

use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column\CodeColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new CodeColumn());
        
        $id = $this->getColumnByKey('t5.code');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
