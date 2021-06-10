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

class QuantityColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't2.quantity';
    }

    public function getColumnName()
    {
        return t('Quantity');
    }

    public function getColumnCallback()
    {
        return 'getQuantity';
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
        $where = sprintf('t2.quantity %s :quantity', $sort);
        $query->setParameter('quantity', $mixed->getQuantity());
        $query->andWhere($where);
    }
}
