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

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Context\FrontendFormContext;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\File\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\Product as ProductEntity;

class Products extends DashboardPageController
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
     * @param ProductEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data, $entry)) {
            $entry->setName($data["name"]);
            $entry->setHandle($data["handle"]);
            $entry->setShortDescription($data["shortDescription"]);
            $entry->setDescription($data["description"]);
            $entry->setLocale($data["locale"]);
            $entry->setPriceRegular((float)$data["priceRegular"]);
            $entry->setPriceDiscounted((float)$data["priceDiscounted"]);
            $entry->setTaxRate($this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $data["taxRate"]]));
            $entry->setShippingCost($this->entityManager->getRepository(ShippingCost::class)->findOneBy(["id" => $data["shippingCost"]]));
            $entry->setCategory($this->entityManager->getRepository(Category::class)->findOneBy(["id" => $data["category"]]));
            $entry->setImage(File::getByID($data["image"]));
            $entry->setQuantity((int)$data["quantity"]);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $categoryEntity = $service->getByHandle('product');
            /** @var ProductCategory $category */
            $category = $categoryEntity->getController();
            $setManager = $category->getSetManager();

            foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                $controller = $ak->getController();
                $value = $controller->createAttributeValueFromRequest();
                $entry->setAttribute($ak, $value);
            }

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/products/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }

    private function setDefaults($entry = null)
    {
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('product');
        /** @var ProductCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        /** @var ProductKey[] $attributes */
        $attributes = [];

        foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
            $attributes[] = $ak;
        }

        $this->set('attributes', $attributes);
        $this->set('renderer', new Renderer(new FrontendFormContext(), $entry));

        $categoryValues = [];
        /** @var Category[] $categoriesEntities */
        $categoriesEntities = $this->entityManager->getRepository(Category::class)->findAll();

        foreach ($categoriesEntities as $categoriesEntity) {
            $categoryValues[$categoriesEntity->getId()] = $categoriesEntity->getName();
        }

        $this->set("categoryValues", $categoryValues);

        $taxRateValues = [];
        /** @var TaxRate[] $taxRateEntities */
        $taxRateEntities = $this->entityManager->getRepository(TaxRate::class)->findAll();

        foreach ($taxRateEntities as $taxRateEntity) {
            $taxRateValues[$taxRateEntity->getId()] = $taxRateEntity->getName();
        }

        $this->set("taxRateValues", $taxRateValues);
        $shippingCostValues = [];
        /** @var ShippingCost[] $shippingCostEntities */
        $shippingCostEntities = $this->entityManager->getRepository(ShippingCost::class)->findAll();

        foreach ($shippingCostEntities as $shippingCostEntity) {
            $shippingCostValues[$shippingCostEntity->getId()] = $shippingCostEntity->getName();
        }

        /** @var \Concrete\Core\Entity\Site\Site $site */
        $site = $this->app->make('site')->getActiveSiteForEditing();
        $locales = [];
        foreach ($site->getLocales() as $localeEntity) {
            $locales[$localeEntity->getLocale()] = sprintf('%s (%s)', $localeEntity->getLanguageText(), $localeEntity->getLocale());
        }

        $this->set("locales", $locales);
        $this->set("shippingCostValues", $shippingCostValues);
        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/products/edit");
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
     * @param Product $entry
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

        if (!isset($data["shortDescription"]) || empty($data["shortDescription"])) {
            $this->error->add(t("The field \"Short Description\" is required."));
        }

        if (!isset($data["description"]) || empty($data["description"])) {
            $this->error->add(t("The field \"Description\" is required."));
        }

        if (!isset($data["priceRegular"]) || empty($data["priceRegular"])) {
            $this->error->add(t("The field \"Price Regular\" is required."));
        }

        $existingProduct = $this->entityManager->getRepository(Product::class)->findOneBy(["handle" => $data["handle"], "locale" => $data["locale"]]);

        if ($existingProduct instanceof Product && $existingProduct->getId() !== $entry->getId()) {
            $this->error->add(t("The given handle is already in use."));
        }

        return !$this->error->has();
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new ProductEntity();

        if ($this->token->validate("save_product_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function edit($id = null)
    {
        /** @var ProductEntity $entry */
        $entry = $this->entityManager->getRepository(ProductEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof ProductEntity) {
            if ($this->token->validate("save_product_entity")) {
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
        /** @var ProductEntity $entry */
        $entry = $this->entityManager->getRepository(ProductEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof ProductEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/products/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Products\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\Products $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\Products::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
