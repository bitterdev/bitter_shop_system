<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\TaxRate\Search\ColumnSet;

use Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Column\HandleColumn;
use Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Column\IdColumn;

class Available extends DefaultSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new IdColumn());
        $this->addColumn(new HandleColumn());
    }
}
