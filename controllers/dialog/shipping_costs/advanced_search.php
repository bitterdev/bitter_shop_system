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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\ShippingCosts;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Bitter\BitterShopSystem\Entity\Search\SavedShippingCostSearch;
use Bitter\BitterShopSystem\ShippingCost\Search\SearchProvider;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;
    
    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
    
    public function on_before_render()
    {
        parent::on_before_render();
        
        // use core views (remove package handle)
        $viewObject = $this->getViewObject();
        $viewObject->setInnerContentFile(null);
        $viewObject->setPackageHandle(null);
        $viewObject->setupRender();
    }
    
    public function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }
    
    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedShippingCostSearch::class);
        }
        
        return null;
    }
    
    public function getFieldManager()
    {
        return ManagerFactory::get('shipping_cost');
    }
    
    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/search/shipping_costs/preset', $search->getID());
    }
    
    public function getCurrentSearchBaseURL()
    {
        return Url::to('/ccm/system/search/shipping_costs/current');
    }
    
    public function getBasicSearchBaseURL()
    {
        return Url::to('/ccm/system/search/shipping_costs/basic');
    }
    
    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/shipping_costs/advanced_search/preset/delete?presetID=' . $search->getID());
    }
    
    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/shipping_costs/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}