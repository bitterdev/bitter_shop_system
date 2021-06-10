<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\ColumnSet\Column;

use Bitter\BitterShopSystem\Customer\Search\Field\Manager;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Customer\CustomerList;

class UserColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't3.user';
    }
    
    public function getColumnName()
    {
        return t('User');
    }
    
    public function getColumnCallback()
    {
        return [Manager::class, 'getUser'];
    }
    
    /**
    * @param CustomerList $itemList
    * @param $mixed Customer
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t3.user %s :user', $sort);
        $query->setParameter('user', $mixed->getUser());
        $query->andWhere($where);
    }
}
