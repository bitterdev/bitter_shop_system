<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\ColumnSet;

use Bitter\BitterShopSystem\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\Column\EmailColumn;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\Column\IdColumn;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\Column\UserColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = CustomerKey::class;
    
    public function __construct()
    {
        $this->addColumn(new IdColumn());
        $this->addColumn(new EmailColumn());
        $this->addColumn(new UserColumn());
        
        $id = $this->getColumnByKey('t3.id');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
