<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Column;

use Bitter\BitterShopSystem\TaxRate\Search\Field\Manager;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\TaxRate\TaxRateList;

class RateColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.rate';
    }
    
    public function getColumnName()
    {
        return t('Rate');
    }
    
    public function getColumnCallback()
    {
        return [Manager::class, 'getRate'];
    }
    
    /**
    * @param TaxRateList $itemList
    * @param $mixed TaxRate
    * @noinspection PhpDocSignatureInspection
    */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t0.rate %s :rate', $sort);
        $query->setParameter('rate', $mixed->getRate());
        $query->andWhere($where);
    }
}
