<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Category\Search\ColumnSet;

use Bitter\BitterShopSystem\Category\Search\ColumnSet\Column\NameColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new NameColumn());
        
        $id = $this->getColumnByKey('t6.name');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
