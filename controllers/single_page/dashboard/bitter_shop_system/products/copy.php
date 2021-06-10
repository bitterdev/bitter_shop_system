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

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;

class Copy extends DashboardPageController
{
    public function view()
    {
        /** @var Validation $formValidation */
        $formValidation = $this->app->make(Validation::class);
        /** @var ProductService $productService */
        $productService = $this->app->make(ProductService::class);
        /** @var Site $site */
        $site = $this->app->make('site')->getActiveSiteForEditing();
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('product');
        /** @var ProductCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        $locales = [];

        foreach ($site->getLocales() as $localeEntity) {
            $locales[$localeEntity->getLocale()] = sprintf('%s (%s)', $localeEntity->getLanguageText(), $localeEntity->getLocale());
        }

        if ($this->request->getMethod() === "POST") {
            $formValidation->setData($this->request->request->all());
            $formValidation->addRequiredToken("copy_products");
            $formValidation->addRequired("sourceLocale", t("You need to select a source locale."));
            $formValidation->addRequired("targetLocale", t("You need to select a target locale."));

            if ($formValidation->test()) {
                $copiedProductsCounter = 0;

                $sourceLocale = (string)$this->request->request->get("sourceLocale");
                $targetLocale = (string)$this->request->request->get("targetLocale");

                /** @var Product[] $productEntries */
                $productEntries = $productService->getAllByLocale($sourceLocale);

                foreach ($productEntries as $sourceProductEntry) {
                    $targetProductEntry = $productService->getByHandleWithLocale($sourceProductEntry->getHandle(), $targetLocale);

                    if (!$targetProductEntry instanceof Product) {
                        $targetProductEntry = clone $sourceProductEntry;
                        $targetProductEntry->setLocale($targetLocale);
                        $this->entityManager->persist($targetProductEntry);
                        $this->entityManager->flush();

                        // copy attributes
                        foreach ($setManager->getUnassignedAttributeKeys() as $attributeKey) {
                            $attributeValue = $sourceProductEntry->getAttributeValue($attributeKey);
                            $targetProductEntry->setAttribute($attributeKey, $attributeValue);
                        }

                        $copiedProductsCounter++;
                    }
                }

                if ($copiedProductsCounter === 0) {
                    $this->error->add(t("All products are already available in the target locale."));
                } else {
                    $this->set('success', t2("You have successfully copied %s product.", "You have successfully copied %s products.", $copiedProductsCounter));
                }
            } else {
                $this->error = $formValidation->getError();
            }
        }

        $this->set('locales', $locales);
        $this->set('error', $this->error);
    }
}