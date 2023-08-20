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

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\Customer as CustomerEntity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Bitter\BitterShopSystem\Entity\Search\SavedCustomerSearch;
use Bitter\BitterShopSystem\Navigation\Breadcrumb\Dashboard\DashboardCustomersBreadcrumbFactory;
use Bitter\BitterShopSystem\Customer\Search\Menu\MenuFactory;
use Bitter\BitterShopSystem\Customer\Search\SearchProvider;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;

class Customers extends DashboardPageController
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
            $this->headerMenu = $this->app->make(ElementManager::class)->get('customers/search/menu', 'bitter_shop_system');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('customers/search/search', 'bitter_shop_system');
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
            $preset = $this->entityManager->find(SavedCustomerSearch::class, $presetID);

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
     * @return DashboardCustomersBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardCustomersBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardCustomersBreadcrumbFactory::class);
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
     * @param CustomerEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data, $entry)) {
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $categoryEntity = $service->getByHandle('customer');
            /** @var CustomerCategory $category */
            $category = $categoryEntity->getController();
            $setManager = $category->getSetManager();

            /** @var UserInfoRepository $userInfoRepository */
            $userInfoRepository = $this->app->make(UserInfoRepository::class);
            $userInfo = $userInfoRepository->getByID($data["user"]);
            $entry->setUser($userInfo ? $userInfo->getEntityObject() : null);
            $entry->setEmail($data["email"]);
            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                $controller = $ak->getController();
                $value = $controller->createAttributeValueFromRequest();
                $entry->setAttribute($ak, $value);
            }

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/customers/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }

    /**
     * @param Customer $entry
     */
    private function setDefaults($entry = null)
    {
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('customer');
        /** @var CustomerCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        /** @var CustomerKey[] $attributes */
        $attributes = [];

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            /** @var CustomerKey $ak */
            $attributes[] = $ak;
        }

        $this->set('attributes', $attributes);
        $this->set('renderer', new Renderer(new FrontendFormContext(), $entry));
        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/customers/edit");
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
     * @param Customer $entry
     * @return bool
     */
    public function validate($data = null, $entry = null)
    {
        $customerRepository = $this->entityManager->getRepository(Customer::class);

        if (!isset($data["email"]) || empty($data["email"])) {
            $this->error->add(t("The field \"Email\" is required."));
        }

        if (strlen($data["email"]) > 0) {
            $existingCustomer = $customerRepository->findOneBy(["email" => $data["email"]]);

            if ($existingCustomer instanceof Customer) {
                if ($existingCustomer->getId() !== $entry->getId()) {
                    $this->error->add(t("There is already an customer account existing that is associated with the given mail address."));
                }
            }
        }

        $userAccount = User::getByUserID($data["user"]);

        if ($userAccount instanceof User) {
            $existingCustomer = $customerRepository->findOneBy(["user" => $userAccount->getUserInfoObject()->getEntityObject()]);

            if ($existingCustomer instanceof Customer) {
                if ($existingCustomer->getId() !== $entry->getId()) {
                    $this->error->add(t("There is already an customer account existing that is associated with the given user account."));
                }
            }
        }
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('customer');
        /** @var CustomerCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            /** @var CustomerKey $ak */
            $controller = $ak->getController();

            if (method_exists($controller, 'validateForm')) {
                $controller->setRequest($this->request);
                $validateResponse = $controller->validateForm($controller->post());

                if (!$validateResponse) {
                    $this->error->add(t("The field \"%s\" is required.", $ak->getAttributeKeyName()));
                }
            }
        }

        return !$this->error->has();
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new CustomerEntity();

        if ($this->token->validate("save_customer_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function update($id = null)
    {
        /** @var CustomerEntity $entry */
        $entry = $this->entityManager->getRepository(CustomerEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CustomerEntity) {
            if ($this->token->validate("save_customer_entity")) {
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
        /** @var CustomerEntity $entry */
        $entry = $this->entityManager->getRepository(CustomerEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CustomerEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/customers/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }
}
