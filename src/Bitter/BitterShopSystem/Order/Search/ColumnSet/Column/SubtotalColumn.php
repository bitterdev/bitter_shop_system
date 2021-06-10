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

class SubTotalColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't4.subtotal';
    }

    public function getColumnName()
    {
        return t('Subtotal');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getSubtotal'];
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
        $where = sprintf('t4.subtotal %s :subtotal', $sort);
        $query->setParameter('subtotal', $mixed->getSubtotal());
        $query->andWhere($where);
    }
}
