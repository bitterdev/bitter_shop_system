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

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Application;

class ProductAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $value = $db->fetchColumn('select ' . $this->getColumnKey() . ' from ProductSearchIndexAttributes where productId = ?', [$mixed->getId()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', t2.productId) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getId());
        $query->andWhere($where);
    }
}
