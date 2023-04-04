<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Coupon\CouponList;

class CodeColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't5.code';
    }
    
    public function getColumnName()
    {
        return t('Code');
    }
    
    public function getColumnCallback()
    {
        return 'getCode';
    }
    
    /**
    * @param CouponList $itemList
    * @param $mixed Coupon
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t5.code %s :code', $sort);
        $query->setParameter('code', $mixed->getCode());
        $query->andWhere($where);
    }
}
