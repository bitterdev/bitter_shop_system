<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Dashboard\BitterShopSystem;

use Bitter\BitterShopSystem\Entity\ShippingCostVariant;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\ShippingCosts\EditVariantHeader;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\ShippingCost as ShippingCostEntity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Bitter\BitterShopSystem\Entity\Search\SavedShippingCostSearch;
use Bitter\BitterShopSystem\Navigation\Breadcrumb\Dashboard\DashboardShippingCostsBreadcrumbFactory;
use Bitter\BitterShopSystem\ShippingCost\Search\Menu\MenuFactory;
use Bitter\BitterShopSystem\ShippingCost\Search\SearchProvider;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;

class ShippingCosts extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;

    /** @var Element */
    protected $headerMenu;
    /** @var Element */
    protected $headerSearch;

    /**
     * @return SearchProvider
     * @throws BindingResolutionException
     */
    protected function getSearchProvider(): SearchProvider
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     * @throws BindingResolutionException
     */
    protected function getQueryFactory(): QueryFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu(): Element
    {
        if (!isset($this->headerMenu)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerMenu = $this->app->make(ElementManager::class)->get('shipping_costs/search/menu', 'bitter_shop_system');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('shipping_costs/search/search', 'bitter_shop_system');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     * @throws BindingResolutionException
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerMenu->getElementController()->setQuery($result->getQuery());
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     * @throws BindingResolutionException
     */
    protected function createSearchResult(Query $query): Result
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));

        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    /**
     * @throws BindingResolutionException
     */
    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    /**
     * @throws BindingResolutionException
     */
    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedShippingCostSearch::class, $presetID);

            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                return;
            }
        }

        $this->view();
    }

    /**
     * @return DashboardShippingCostsBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardShippingCostsBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardShippingCostsBreadcrumbFactory::class);
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);

        $this->headerSearch->getElementController()->setQuery(null);
    }
    
    public function on_start()
    {
        parent::on_start();
        
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
    }
    
    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     * @param ShippingCostEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();
        
        if ($this->validate($data, $entry)) {
            $entry->setName($data["name"]);
            $entry->setPrice($data["price"]);
            $entry->setHandle($data["handle"]);
            if (isset($data["taxRate"])) {
                $entry->setTaxRate($this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $data["taxRate"]]));
            } else {
                $entry->setTaxRate(null);
            }

            $this->entityManager->persist($entry);
            $this->entityManager->flush();
            
            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }
    
    private function setDefaults($entry = null)
    {
        if ($entry instanceof ShippingCostEntity && $entry->getId() > 0) {
            $headerMenu = new EditVariantHeader();
            $headerMenu->set("entry", $entry);
            $this->set('headerMenu', $headerMenu);
        }

        $taxRateValues = [];
        /** @var TaxRate[] $taxRateEntities */
        $taxRateEntities = $this->entityManager->getRepository(TaxRate::class)->findAll();

        foreach ($taxRateEntities as $taxRateEntity) {
            $taxRateValues[$taxRateEntity->getId()] = $taxRateEntity->getName();
        }

        $this->set("taxRateValues", $taxRateValues);
        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/shipping_costs/edit");
    }
    
    public function removed()
    {
        $this->set("success", t('The item has been successfully removed.'));
        $this->view();
    }
    
    public function saved()
    {
        $this->set("success", t('The item has been successfully updated.'));
        $this->view();
    }
    
    /**
     * @noinspection PhpUnusedParameterInspection
     * @param array $data
     * @param ShippingCostEntity Entry
     * @return bool
     */
    public function validate($data = null, $entry = null)
    {
        if (!isset($data["name"]) || empty($data["name"]))
        {
            $this->error->add(t("The field \"Name\" is required."));
        }

        if (!isset($data["handle"]) || empty($data["handle"]))
        {
            $this->error->add(t("The field \"Handle\" is required."));
        }
        
        if (!isset($data["price"]) || empty($data["price"]))
        {
            $this->error->add(t("The field \"Price\" is required."));
        }

        $existingEntry = $this->entityManager->getRepository(ShippingCostEntity::class)->findOneBy(["handle" => $data["handle"]]);

        if ($existingEntry instanceof ShippingCostEntity && $existingEntry->getId() !== $entry->getId()) {
            $this->error->add(t("The given handle is already in use."));
        }
        
        return !$this->error->has();
    }
    
    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new ShippingCostEntity();
        
        if ($this->token->validate("save_shipping_cost_entity")) {
            return $this->save($entry);
        }
        
        $this->setDefaults($entry);
    }

    private function setVariantDefaults($entry)
    {
        $this->set("variant", $entry);
        $this->render("/dashboard/bitter_shop_system/shipping_costs/edit_variant");
    }

    public function validateVariant($data = null, $entry = null)
    {
        if (!isset($data["country"]) || empty($data["country"])) {
            $this->error->add(t("The field \"Country\" is required."));
        }

        if (!isset($data["variantPrice"]) || empty($data["variantPrice"])) {
            $this->error->add(t("The field \"Price\" is required."));
        }

        return !$this->error->has();
    }

    /**
     * @param ShippingCostVariant $entry
     * @return \Concrete\Core\Routing\RedirectResponse|Response
     */
    private function saveVariant($entry)
    {
        $data = $this->request->request->all();

        if ($this->validateVariant($data, $entry)) {
            $entry->setState((string)$data["state_province"]);
            $entry->setCountry($data["country"]);
            $entry->setPrice($data["variantPrice"]);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/update", $entry->getShippingCost()->getId(), "variant_updated"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setVariantDefaults($entry);
    }

    public function remove_variant($variantId = null)
    {
        /** @var ShippingCostVariant $entry */
        $entry = $this->entityManager->getRepository(ShippingCostVariant::class)->findOneBy([
            "id" => $variantId
        ]);

        if ($entry instanceof ShippingCostVariant) {
            $shippingCostId = $entry->getShippingCost()->getId();

            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/update", $shippingCostId, "variant_removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function edit_variant($variantId = null)
    {
        /** @var ShippingCostVariant $entry */
        $entry = $this->entityManager->getRepository(ShippingCostVariant::class)->findOneBy([
            "id" => $variantId
        ]);

        if ($entry instanceof ShippingCostVariant) {
            if ($this->token->validate("save_shipping_cost_variant")) {
                return $this->saveVariant($entry);
            }

            $this->setVariantDefaults($entry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function add_variant($id = null)
    {
        /** @var ShippingCostEntity $entry */
        $entry = $this->entityManager->getRepository(ShippingCostEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof ShippingCostEntity) {
            $variantEntry = new ShippingCostVariant();
            $variantEntry->setShippingCost($entry);

            if ($this->token->validate("save_shipping_cost_variant")) {
                return $this->saveVariant($variantEntry);
            }

            $this->setVariantDefaults($variantEntry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function update($id = null, $state = null)
    {
        /** @var ShippingCostEntity $entry */
        $entry = $this->entityManager->getRepository(ShippingCostEntity::class)->findOneBy([
            "id" => $id
        ]);

        switch($state) {
            case "variant_removed":
                $this->set("success", t("The variant has been successfully removed."));
                break;
            case "variant_updated":
                $this->set("success", t("The variant has been successfully updated."));
                break;
        }
        
        if ($entry instanceof ShippingCostEntity) {
            if ($this->token->validate("save_shipping_cost_entity")) {
                return $this->save($entry);
            }
            
            $this->setDefaults($entry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }
    
    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function remove($id = null)
    {
        /** @var ShippingCostEntity $entry */
        $entry = $this->entityManager->getRepository(ShippingCostEntity::class)->findOneBy([
            "id" => $id
        ]);
        
        if ($entry instanceof ShippingCostEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();
            
            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }
}
