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

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostList;

class NameColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't1.name';
    }
    
    public function getColumnName()
    {
        return t('Name');
    }
    
    public function getColumnCallback()
    {
        return 'getName';
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
        $where = sprintf('t1.name %s :name', $sort);
        $query->setParameter('name', $mixed->getName());
        $query->andWhere($where);
    }
}
