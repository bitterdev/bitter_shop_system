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

namespace Bitter\BitterShopSystem\Customer\Search;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Search\SavedCustomerSearch;
use Bitter\BitterShopSystem\Customer\CustomerList;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\DefaultSet;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\Available;
use Bitter\BitterShopSystem\Customer\Search\ColumnSet\ColumnSet;
use Bitter\BitterShopSystem\Customer\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    /**
     * @var CustomerCategory
     */
    protected $category;

    public function __construct(
        Session $session,
        CustomerCategory $category
    )
    {
        parent::__construct($session);

        $this->category = $category;
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('customer');
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
    
    public function getSessionNamespace()
    {
        return 'customer';
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
        return new CustomerList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedCustomerSearch();
    }
}
