<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\ColumnSet;


use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\CategoryColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\DescriptionColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\IdColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ImageColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\QuantityColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ShortDescriptionColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\HandleColumn;

class Available extends DefaultSet
{
    public function __construct()
    {
        parent::__construct();

        $this->addColumn(new IdColumn());
        $this->addColumn(new HandleColumn());
        $this->addColumn(new ShortDescriptionColumn());
        $this->addColumn(new DescriptionColumn());
        $this->addColumn(new QuantityColumn());
        $this->addColumn(new ImageColumn());
        $this->addColumn(new CategoryColumn());
    }
}
