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

namespace Bitter\BitterShopSystem\Category\Search;

use Bitter\BitterShopSystem\Entity\Search\SavedCategorySearch;
use Bitter\BitterShopSystem\Category\CategoryList;
use Bitter\BitterShopSystem\Category\Search\ColumnSet\DefaultSet;
use Bitter\BitterShopSystem\Category\Search\ColumnSet\Available;
use Bitter\BitterShopSystem\Category\Search\ColumnSet\ColumnSet;
use Bitter\BitterShopSystem\Category\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('category');
    }
    
    public function getSessionNamespace()
    {
        return 'category';
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
        return new CategoryList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedCategorySearch();
    }
}
