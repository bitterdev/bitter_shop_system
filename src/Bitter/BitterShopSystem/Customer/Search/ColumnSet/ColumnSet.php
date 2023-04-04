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
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\Column\CustomerAttributeKeyColumn;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Search\Column\Set;
use Bitter\BitterShopSystem\Customer\Search\SearchProvider;

class ColumnSet extends Set
{
    protected $attributeClass = CustomerKey::class;

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new CustomerAttributeKeyColumn($ak);
        return $col;
    }

    public static function getCurrent()
    {
        $app = Facade::getFacadeApplication();
        /** @var $provider SearchProvider */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();
        
        if ($query) {
            return $query->getColumns();
        }
        
        return $provider->getDefaultColumnSet();
    }
}
