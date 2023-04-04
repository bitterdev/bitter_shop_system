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

namespace Bitter\BitterShopSystem\Product\Search;

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Search\SavedProductSearch;
use Bitter\BitterShopSystem\Product\ProductList;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\DefaultSet;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Available;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\ColumnSet;
use Bitter\BitterShopSystem\Product\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    /**
     * @var ProductCategory
     */
    protected $category;

    public function __construct(
        Session $session,
        ProductCategory $category
    )
    {
        parent::__construct($session);

        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getCustomAttributeKeys()
     */
    public function getCustomAttributeKeys()
    {
        return $this->category->getSearchableList();
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('product');
    }
    
    public function getSessionNamespace()
    {
        return 'product';
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
        return new ProductList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedProductSearch();
    }
}
