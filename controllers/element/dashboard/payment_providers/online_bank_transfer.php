<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PaymentProviders;

use Bitter\BitterShopSystem\PaymentProvider\PaymentConfigurationInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\ErrorList\ErrorList;

class OnlineBankTransfer extends ElementController implements PaymentConfigurationInterface
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return 'dashboard/payment_providers/online_bank_transfer';
    }

    public function save(): ErrorList
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $errorList = new ErrorList();

        $config->save("bitter_shop_system.payment_providers.online_bank_transfer.config_key", $this->request->request->get("configKey"));
        return $errorList;
    }

    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $this->set("configKey", $config->get("bitter_shop_system.payment_providers.online_bank_transfer.config_key"));
    }

    public function isConfigurationComplete(): bool
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        return $config->has("bitter_shop_system.payment_providers.online_bank_transfer.config_key");
    }
}
