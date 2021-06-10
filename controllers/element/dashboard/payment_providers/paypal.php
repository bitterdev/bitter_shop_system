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

class Paypal extends ElementController implements PaymentConfigurationInterface
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return 'dashboard/payment_providers/paypal';
    }

    public function save(): ErrorList
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $errorList = new ErrorList();

        $config->save("bitter_shop_system.payment_providers.paypal.selected_mode", $this->request->request->get("selectedMode"));
        $config->save("bitter_shop_system.payment_providers.paypal.client_id", $this->request->request->get("clientId"));
        $config->save("bitter_shop_system.payment_providers.paypal.client_secret", $this->request->request->get("clientSecret"));

        return $errorList;
    }

    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $availableModes = [
            "production" => t("Production"),
            "sandbox" => t("Sandbox")
        ];

        $this->set("availableModes", $availableModes);
        $this->set("selectedMode", $config->get("bitter_shop_system.payment_providers.paypal.selected_mode", "sandbox"));
        $this->set("clientId", $config->get("bitter_shop_system.payment_providers.paypal.client_id"));
        $this->set("clientSecret", $config->get("bitter_shop_system.payment_providers.paypal.client_secret"));
    }

    public function isConfigurationComplete(): bool
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        return
            $config->has("bitter_shop_system.payment_providers.paypal.selected_mode") &&
            $config->has("bitter_shop_system.payment_providers.paypal.client_id") &&
            $config->has("bitter_shop_system.payment_providers.paypal.client_secret");
    }
}
