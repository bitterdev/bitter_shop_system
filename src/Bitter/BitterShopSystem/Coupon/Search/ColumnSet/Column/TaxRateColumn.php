<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Coupon\Search\Field\Manager;
use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Coupon\CouponList;

class TaxRateColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't5.taxRate';
    }
    
    public function isColumnSortable()
    {
        return false;
    }
    
    public function getColumnName()
    {
        return t('Tax Rate');
    }
    
    public function getColumnCallback()
    {
        return [Manager::class, 'getTaxRate'];
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
        $where = sprintf('t2.tax_rate %s :tax_rate', $sort);
        $query->setParameter('tax_rate', $mixed->getTaxRate());
        $query->andWhere($where);
    }
}
