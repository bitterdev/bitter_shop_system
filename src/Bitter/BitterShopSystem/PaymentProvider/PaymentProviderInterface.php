<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

interface PaymentProviderInterface
{
    public function getConfigurationElement(): ?PaymentConfigurationInterface;

    public function getName(): string;

    public function getHandle(): string;

    public function processPayment(): void;

    public function processPaymentNotification(): void;

    public function on_start(): void;
}