<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Attribute\Key;

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Concrete\Core\Support\Facade\Facade;

class ProductKey extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return ProductCategory::class;
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }

    public static function getByID($akID)
    {
        return static::getFacadeRoot()->getAttributeKeyByID($akID);
    }
}
