<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Dashboard\BitterShopSystem;

use Bitter\BitterShopSystem\Enumerations\CurrencySymbolPositions;
use Bitter\BitterShopSystem\PaymentProvider\PaymentConfigurationInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostService as ShippingCostService;
use Bitter\BitterShopSystem\TaxRate\TaxRateService as TaxRateService;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{
    /** @var Repository */
    protected $config;
    /** @var Validation */
    protected $formValidator;
    /** @var TaxRateService */
    protected $taxRateService;
    /** @var ShippingCostService */
    protected $shippingCostService;
    /** @var PaymentProviderService */
    protected $paymentProviderService;

    public function on_start()
    {
        parent::on_start();
        $this->config = $this->app->make(Repository::class);
        $this->taxRateService = $this->app->make(TaxRateService::class);
        $this->shippingCostService = $this->app->make(ShippingCostService::class);
        $this->formValidator = $this->app->make(Validation::class);
        $this->paymentProviderService = $this->app->make(PaymentProviderService::class);
    }

    public function view()
    {
        $paymentProviders = $this->paymentProviderService->getPaymentProviders();

        if ($this->request->getMethod() === "POST") {
            $this->formValidator->setData($this->request->request->all());
            $this->formValidator->addRequiredToken("update_settings");
            $this->formValidator->addRequiredEmail("notificationMailAddress", t("You need to enter a valid notification mail address."));
            $this->formValidator->addRequired("currencySymbol", t("You need to enter a currency symbol."));
            $this->formValidator->addRequired("currencyCode", t("You need to enter a currency code."));
            $this->formValidator->addRequired("currencySymbolPosition", t("You need to enter the symbol position."));
            $this->formValidator->addRequired("currencySymbolSpaces", t("You need to enter the symbol spaces."));
            $this->formValidator->addRequired("decimals", t("You need to enter the decimals."));
            $this->formValidator->addRequired("decimalPoint", t("You need to enter a decimal point."));
            $this->formValidator->addRequired("thousandsSeparator", t("You need to enter a thousands separator."));

            if ($this->formValidator->test()) {
                $this->config->save("bitter_shop_system.display_prices_including_tax", $this->request->request->has("displayPricesIncludingTax"));
                $this->config->save("bitter_shop_system.notification_mail_address", (string)$this->request->request->get("notificationMailAddress"));
                $this->config->save("bitter_shop_system.terms_of_use_page_id", (int)$this->request->request->get("termsOfUsePageId"));
                $this->config->save("bitter_shop_system.privacy_policy_page_id", (int)$this->request->request->get("privacyPolicyPageId"));
                $this->config->save("bitter_shop_system.money_formatting.currency_symbol", (string)$this->request->request->get("currencySymbol"));
                $this->config->save("bitter_shop_system.money_formatting.currency_code", (string)$this->request->request->get("currencyCode"));
                $this->config->save("bitter_shop_system.money_formatting.currency_symbol_position", (string)$this->request->request->get("currencySymbolPosition"));
                $this->config->save("bitter_shop_system.money_formatting.currency_symbol_spaces", (int)$this->request->request->get("currencySymbolSpaces"));
                $this->config->save("bitter_shop_system.money_formatting.decimals", (int)$this->request->request->get("decimals"));
                $this->config->save("bitter_shop_system.money_formatting.decimal_point", (string)$this->request->request->get("decimalPoint"));
                $this->config->save("bitter_shop_system.money_formatting.thousands_separator", (string)$this->request->request->get("thousandsSeparator"));

                foreach ($paymentProviders as $paymentProvider) {
                    if ($paymentProvider->getConfigurationElement() instanceof PaymentConfigurationInterface) {
                        foreach ($paymentProvider->getConfigurationElement()->save() as $error) {
                            $this->error->add($error);
                        }
                    }
                }

                if (!$this->error->has()) {
                    $this->set("success", t("The settings has been successfully updated."));
                }
            } else {
                /** @var ErrorList $errorList */
                $errorList = $this->formValidator->getError();

                foreach ($errorList->getList() as $error) {
                    $this->error->add($error);
                }
            }
        }

        $this->set("currencySymbolPositions", [
            CurrencySymbolPositions::POS_LEFT => t("Left"),
            CurrencySymbolPositions::POS_RIGHT => t("Right")
        ]);

        $this->set("displayPricesIncludingTax", (bool)$this->config->get("bitter_shop_system.display_prices_including_tax", false));
        $this->set("notificationMailAddress", (string)$this->config->get("bitter_shop_system.notification_mail_address"));
        $this->set("termsOfUsePageId", (int)$this->config->get("bitter_shop_system.terms_of_use_page_id"));
        $this->set("privacyPolicyPageId", (int)$this->config->get("bitter_shop_system.privacy_policy_page_id"));
        $this->set("currencySymbol", (string)$this->config->get("bitter_shop_system.money_formatting.currency_symbol", "$"));
        $this->set("currencyCode", (string)$this->config->get("bitter_shop_system.money_formatting.currency_code", "USD"));
        $this->set("currencySymbolPosition", (string)$this->config->get("bitter_shop_system.money_formatting.currency_symbol_position", CurrencySymbolPositions::POS_LEFT));
        $this->set("currencySymbolSpaces", (int)$this->config->get("bitter_shop_system.money_formatting.currency_symbol_spaces", 1));
        $this->set("decimals", (int)$this->config->get("bitter_shop_system.money_formatting.decimals", 2));
        $this->set("decimalPoint", (string)$this->config->get("bitter_shop_system.money_formatting.decimal_point", "."));
        $this->set("thousandsSeparator", (string)$this->config->get("bitter_shop_system.money_formatting.thousands_separator", ","));
        $this->set("paymentProviders", $paymentProviders);
    }
}