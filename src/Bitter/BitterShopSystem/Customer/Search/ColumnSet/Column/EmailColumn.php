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

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Customer\CustomerList;

class EmailColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't3.email';
    }

    public function getColumnName()
    {
        return t('Email');
    }

    public function getColumnCallback()
    {
        return 'getEmail';
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
        $where = sprintf('t3.email %s :email', $sort);
        $query->setParameter('email', $mixed->getEmail());
        $query->andWhere($where);
    }
}
