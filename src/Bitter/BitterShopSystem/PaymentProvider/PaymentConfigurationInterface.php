<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

use Concrete\Core\Error\ErrorList\ErrorList;

interface PaymentConfigurationInterface
{
    public function save(): ErrorList;

    public function isConfigurationComplete(): bool;

    public function render();
}