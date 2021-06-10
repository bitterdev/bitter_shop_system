<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\ColumnSet;

use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\PaymentReceivedDateColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\PaymentReceivedColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\PaymentProviderColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\TransactionIdColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\CustomerColumn;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Column\IdColumn;

class Available extends DefaultSet
{
    public function __construct()
    {
        parent::__construct();

        $this->addColumn(new IdColumn());
        $this->addColumn(new CustomerColumn());
        $this->addColumn(new PaymentReceivedDateColumn());
        $this->addColumn(new TransactionIdColumn());
        $this->addColumn(new PaymentReceivedColumn());
        $this->addColumn(new PaymentProviderColumn());
    }

}
