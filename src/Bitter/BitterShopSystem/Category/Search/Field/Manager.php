<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Category\Search\Field;

use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Category\Search\Field\Field\HandleField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\BitterShopSystem\Category\Search\Field\Field\NameField;

class Manager extends FieldManager
{
    public function getRate(Category $rate)
    {
        return number_format($rate->getRate()) . "%";
    }

    public function __construct()
    {
        $properties = [
            new NameField(),
            new HandleField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
