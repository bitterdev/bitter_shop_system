<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Settings\OrderConfirmation;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement()
    {
        return "dashboard/settings/order_confirmation/header";
    }
}
