<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\ShippingCost\Search\ColumnSet\Column;

use Bitter\BitterShopSystem\ShippingCost\Search\Field\Manager;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostList;

class PriceColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't1.price';
    }

    public function getColumnName()
    {
        return t('Price');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getPrice'];
    }

    /**
     * @param ShippingCostList $itemList
     * @param $mixed ShippingCost
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t1.price %s :price', $sort);
        $query->setParameter('price', $mixed->getPrice());
        $query->andWhere($where);
    }
}
