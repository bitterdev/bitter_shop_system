<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\ColumnSet\Column;

use Bitter\BitterShopSystem\Order\Search\Field\Manager;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Order\OrderList;
use DateTime;

class PaymentReceivedDateColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't4.paymentReceivedDate';
    }

    public function getColumnName()
    {
        return t('Payment Received Date');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getPaymentReceivedDate'];
    }

    /**
     * @param OrderList $itemList
     * @param Order $mixed
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t4.paymentReceivedDate %s :sortDate', $sort);
        $date = $mixed->getPaymentReceivedDate();
        if ($date instanceof DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }
        $query->setParameter('sortDate', $date);
        $query->andWhere($where);
    }
}
