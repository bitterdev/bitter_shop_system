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

use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ProductAttributeKeyColumn;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Search\Column\Set;
use Bitter\BitterShopSystem\Product\Search\SearchProvider;

class ColumnSet extends Set
{
    protected $attributeClass = ProductKey::class;

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new ProductAttributeKeyColumn($ak);
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
