<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\TaxRate\Search\Field;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\TaxRate\Search\Field\Field\HandleField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\BitterShopSystem\TaxRate\Search\Field\Field\NameField;
use Bitter\BitterShopSystem\TaxRate\Search\Field\Field\RateField;

class Manager extends FieldManager
{
    public function getRate(TaxRate $rate)
    {
        return number_format($rate->getRate(), 2) . "%";
    }

    public function __construct()
    {
        $properties = [
            new NameField(),
            new RateField(),
            new HandleField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
