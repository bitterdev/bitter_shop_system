<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Bitter\BitterShopSystem\Order\Search;

use Bitter\BitterShopSystem\Entity\Search\SavedOrderSearch;
use Bitter\BitterShopSystem\Order\OrderList;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\DefaultSet;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\Available;
use Bitter\BitterShopSystem\Order\Search\ColumnSet\ColumnSet;
use Bitter\BitterShopSystem\Order\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('order');
    }
    
    public function getSessionNamespace()
    {
        return 'order';
    }
    
    public function getCustomAttributeKeys()
    {
        return [];
    }
    
    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }
    
    public function getAvailableColumnSet()
    {
        return new Available();
    }
    
    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }
    
    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }
    
    public function getItemList()
    {
        return new OrderList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedOrderSearch();
    }
}
