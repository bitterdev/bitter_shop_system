<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Dashboard\BitterShopSystem\Products;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\Category as CategoryEntity;

class Categories extends DashboardPageController
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
     * @param CategoryEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data, $entry)) {
            $entry->setName($data["name"]);
            $entry->setHandle($data["handle"]);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/products/categories/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }

    private function setDefaults($entry = null)
    {
        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/products/categories/edit");
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
     * @param CategoryEntity $entry
     * @return bool
     */
    public function validate($data = null, $entry = null)
    {
        if (!isset($data["name"]) || empty($data["name"])) {
            $this->error->add(t("The field \"Name\" is required."));
        }

        if (!isset($data["handle"]) || empty($data["handle"])) {
            $this->error->add(t("The field \"Handle\" is required."));
        }

        $existingEntry = $this->entityManager->getRepository(CategoryEntity::class)->findOneBy(["handle" => $data["handle"]]);

        if ($existingEntry instanceof CategoryEntity && $existingEntry->getId() !== $entry->getId()) {
            $this->error->add(t("The given handle is already in use."));
        }

        return !$this->error->has();
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new CategoryEntity();

        if ($this->token->validate("save_category_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function edit($id = null)
    {
        /** @var CategoryEntity $entry */
        $entry = $this->entityManager->getRepository(CategoryEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CategoryEntity) {
            if ($this->token->validate("save_category_entity")) {
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
        /** @var CategoryEntity $entry */
        $entry = $this->entityManager->getRepository(CategoryEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof CategoryEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/products/categories/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Categories\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\Categories $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\Categories::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
