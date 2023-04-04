<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Category\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Category\CategoryList;

class HandleColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't6.handle';
    }
    
    public function getColumnName()
    {
        return t('Handle');
    }
    
    public function getColumnCallback()
    {
        return 'getHandle';
    }
    
    /**
    * @param CategoryList $itemList
    * @param $mixed Category
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t6.handle %s :handle', $sort);
        $query->setParameter('handle', $mixed->getHandle());
        $query->andWhere($where);
    }
}
