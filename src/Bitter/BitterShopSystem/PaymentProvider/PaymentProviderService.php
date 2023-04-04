<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;

class PaymentProviderService
{
    protected $config;
    protected $app;

    public function __construct(
        Repository $config,
        Application $app
    )
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * @return PaymentProviderInterface[]
     */
    public function getPaymentProviders(): array
    {
        $paymentProviders = [];

        foreach ($this->config->get("bitter_shop_system.payment_providers.all_providers", [
            "bank_transfer" => BankTransfer::class,
            "paypal" => Paypal::class,
            "online_bank_transfer" => OnlineBankTransfer::class
        ]) as $paymentProvider) {
            $paymentProviders[] = $this->app->make($paymentProvider);
        }

        return $paymentProviders;
    }

    /**
     * @return PaymentProviderInterface[]
     */
    public function getAvailablePaymentProviders(): array
    {
        $paymentProviders = [];

        foreach ($this->getPaymentProviders() as $paymentProvider) {
            if ($paymentProvider->getConfigurationElement() === null ||
                $paymentProvider->getConfigurationElement()->isConfigurationComplete()) {
                $paymentProviders[] = $paymentProvider;
            }
        }

        return $paymentProviders;
    }

    public function getByHandle(string $handle): ?PaymentProviderInterface
    {
        foreach ($this->getPaymentProviders() as $paymentProvider) {
            if ($paymentProvider->getHandle() === $handle) {
                return $paymentProvider;
            }
        }

        return null;
    }

    public function getList(): array
    {
        $listItems = [];

        foreach ($this->getPaymentProviders() as $paymentProvider) {
            $listItems[$paymentProvider->getHandle()] = $paymentProvider->getName();
        }

        return $listItems;
    }
}