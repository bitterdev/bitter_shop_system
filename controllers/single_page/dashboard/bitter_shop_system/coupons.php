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

use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\Coupon as CouponEntity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Bitter\BitterShopSystem\Entity\Search\SavedCouponSearch;
use Bitter\BitterShopSystem\Navigation\Breadcrumb\Dashboard\DashboardCouponsBreadcrumbFactory;
use Bitter\BitterShopSystem\Coupon\Search\Menu\MenuFactory;
use Bitter\BitterShopSystem\Coupon\Search\SearchProvider;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;

class Coupons extends DashboardPageController
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
            $this->headerMenu = $this->app->make(ElementManager::class)->get('coupons/search/menu', 'bitter_shop_system');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('coupons/search/search', 'bitter_shop_system');
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
            $preset = $this->entityManager->find(SavedCouponSearch::class, $presetID);

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
     * @return DashboardCouponsBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardCouponsBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardCouponsBreadcrumbFactory::class);
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
     * @param CouponEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();
        /** @var DateTime $dateWidget */
        $dateWidget = $this->app->make(DateTime::class);

        if ($this->validate($data, $entry)) {
            $entry->setCode($data["code"]);
            $entry->setValidFrom($dateWidget->translate("validFrom", $data, true));
            $entry->setValidTo($dateWidget->translate("validTo", $data, true));
            $entry->setUsePercentageDiscount(isset($data["usePercentageDiscount"]));
            $entry->setDiscountPrice((float)$data["discountPrice"]);
            $entry->setDiscountPercentage((float)$data["discountPercentage"]);
            $entry->setMaximumDiscountAmount((float)$data["maximumDiscountAmount"]);
            $entry->setMinimumOrderAmount((float)$data["minimumOrderAmount"]);
            $entry->setLimitQuantity(isset($data["limitQuantity"]));
            $entry->setQuantity((int)$data["quantity"]);
            $entry->setExcludeDiscountedProducts(isset($data["excludeDiscountedProducts"]));

            if (isset($data["taxRate"])) {
                $entry->setTaxRate($this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $data["taxRate"]]));
            } else {
                $entry->setTaxRate(null);
            }

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/coupons/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }

    private function setDefaults($entry = null)
    {
        $taxRateValues = [];
        /** @var TaxRate[] $taxRateEntities */
        $taxRateEntities = $this->entityManager->getRepository(TaxRate::class)->findAll();

        foreach ($taxRateEntities as $taxRateEntity) {
            $taxRateValues[$taxRateEntity->getId()] = $taxRateEntity->getName();
        }

        $this->set("taxRateValues", $taxRateValues);
        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/coupons/edit");
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
     * @param CouponEntity $entry
     * @return bool
     */
    public function validate($data = null, $entry = null)
    {
        if (!isset($data["code"]) || empty($data["code"])) {
            $this->error->add(t("The field \"Code\" is required."));
        }

        if (isset($data["usePercentageDiscount"])) {
            if (!isset($data["discountPercentage"]) || empty($data["discountPercentage"])) {
                $this->error->add(t("The field \"Discount Percentage\" is required."));
            }
        } else {
            if (!isset($data["discountPrice"]) || empty($data["discountPrice"])) {
                $this->error->add(t("The field \"Discount Price\" is required."));
            }
        }

        if (isset($data["limitQuantity"])) {
            if (!isset($data["quantity"]) || empty($data["quantity"])) {
                $this->error->add(t("The field \"Quantity\" is required."));
            }
        }

        $existingEntry = $this->entityManager->getRepository(CouponEntity::class)->findOneBy(["code" => $data["code"]]);

        if ($existingEntry instanceof CouponEntity && $existingEntry->getId() !== $entry->getId()) {
            $this->error->add(t("The given code is already in use."));
        }

        return !$this->error->has();
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new CouponEntity();

        if ($this->token->validate("save_coupon_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function update($id = null)
    {
        /** @var CouponEntity $entry */
        $entry = $this->entityManager->getRepository(CouponEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CouponEntity) {
            if ($this->token->validate("save_coupon_entity")) {
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
        /** @var CouponEntity $entry */
        $entry = $this->entityManager->getRepository(CouponEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CouponEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/coupons/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }
}
