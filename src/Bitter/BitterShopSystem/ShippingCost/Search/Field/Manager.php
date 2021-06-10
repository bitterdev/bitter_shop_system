<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\ShippingCost\Search\Field;

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\ShippingCost\Search\Field\Field\HandleField;
use Bitter\BitterShopSystem\ShippingCost\Search\Field\Field\TaxRateField;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\BitterShopSystem\ShippingCost\Search\Field\Field\NameField;
use Bitter\BitterShopSystem\ShippingCost\Search\Field\Field\PriceField;
use Concrete\Core\Support\Facade\Application;

class Manager extends FieldManager
{
    public function getTaxRate(ShippingCost $shippingCost): string
    {
        if ($shippingCost->getTaxRate() instanceof TaxRate) {
            return $shippingCost->getTaxRate()->getName();
        } else {
            return '';
        }
    }

    public function getPrice(ShippingCost $shippingCost): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($shippingCost->getPrice());
    }

    public function __construct()
    {
        $properties = [
            new NameField(),
            new PriceField(),
            new HandleField(),
            new TaxRateField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
