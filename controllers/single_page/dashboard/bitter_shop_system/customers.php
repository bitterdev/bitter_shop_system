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

class Customers extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;

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
    public function edit($id = null)
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

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Customers\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\Customers $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\Customers::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
