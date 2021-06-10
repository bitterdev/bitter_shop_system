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

class TaxColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't4.tax';
    }

    public function getColumnName()
    {
        return t('Tax');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getTax'];
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
        $where = sprintf('t4.tax %s :tax', $sort);
        $query->setParameter('tax', $mixed->getTax());
        $query->andWhere($where);
    }
}
