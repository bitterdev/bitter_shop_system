<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Customers;

use Concrete\Core\Controller\ElementController;

class SearchHeader extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";
    
    public function getElement()
    {
        return "dashboard/customers/search_header";
    }
}
