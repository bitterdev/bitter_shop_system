<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductList;

class DescriptionColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't2.description';
    }
    
    public function getColumnName()
    {
        return t('Description');
    }
    
    public function getColumnCallback()
    {
        return 'getDescription';
    }
    
    /**
    * @param ProductList $itemList
    * @param $mixed Product
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t2.description %s :description', $sort);
        $query->setParameter('description', $mixed->getDescription());
        $query->andWhere($where);
    }
}
