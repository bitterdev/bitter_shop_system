<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Attribute\Key;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Concrete\Core\Support\Facade\Facade;

class CustomerKey extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return CustomerCategory::class;
    }

    public static function getByHandle($handle)
    {
        return static::getFacadeRoot()->getAttributeKeyByHandle($handle);
    }
}
