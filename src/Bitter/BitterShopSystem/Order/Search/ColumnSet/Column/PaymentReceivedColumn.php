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

class PaymentReceivedColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't4.paymentReceived';
    }

    public function getColumnName()
    {
        return t('Payment Received');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getPaymentReceivedState'];
    }

    /**
     * @param OrderList $itemList
     * @param $mixed Order
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t4.paymentReceived %s :paymentReceived', $sort);
        $query->setParameter('paymentReceived', $mixed->isPaymentReceived());
        $query->andWhere($where);
    }
}
