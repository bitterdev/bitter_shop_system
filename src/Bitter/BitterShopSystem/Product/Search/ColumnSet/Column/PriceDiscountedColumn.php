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

use Bitter\BitterShopSystem\Product\Search\Field\Manager;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductList;

class PriceDiscountedColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't2.priceDiscounted';
    }

    public function getColumnName()
    {
        return t('Price Discounted');
    }

    public function getColumnCallback()
    {
        return [Manager::class, 'getPriceDiscounted'];
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
        $where = sprintf('t2.price_discounted %s :price_discounted', $sort);
        $query->setParameter('price_discounted', $mixed->getPriceDiscounted());
        $query->andWhere($where);
    }
}
