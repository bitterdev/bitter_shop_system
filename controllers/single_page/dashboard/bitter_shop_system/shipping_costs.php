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

class ShippingCosts extends DashboardPageController
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
            $entry->setTaxRate($this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $data["taxRate"]]));
            
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

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/edit", $entry->getShippingCost()->getId(), "variant_updated"), Response::HTTP_TEMPORARY_REDIRECT);
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

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/shipping_costs/edit", $shippingCostId, "variant_removed"), Response::HTTP_TEMPORARY_REDIRECT);
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
    public function edit($id = null, $state = null)
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
    
    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\ShippingCosts\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\ShippingCosts $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\ShippingCosts::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
