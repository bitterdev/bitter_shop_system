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

use Bitter\BitterShopSystem\Entity\TaxRateVariant;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\TaxRates\EditVariantHeader;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\TaxRate as TaxRateEntity;

class TaxRates extends DashboardPageController
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
     * @param TaxRateEntity $entry
     * @return Response
     */
    private function save($entry)
    {
        $data = $this->request->request->all();

        if ($this->validate($data, $entry)) {
            $entry->setName($data["name"]);
            $entry->setRate($data["rate"]);
            $entry->setHandle($data["handle"]);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/tax_rates/saved"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setDefaults($entry);
    }

    private function setDefaults($entry = null)
    {
        if ($entry instanceof TaxRateEntity && $entry->getId() > 0) {
            $headerMenu = new EditVariantHeader();
            $headerMenu->set("entry", $entry);
            $this->set('headerMenu', $headerMenu);
        }

        $this->set("entry", $entry);
        $this->render("/dashboard/bitter_shop_system/tax_rates/edit");
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
     * @param TaxRateEntity $entry
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

        $existingEntry = $this->entityManager->getRepository(TaxRateEntity::class)->findOneBy(["handle" => $data["handle"]]);

        if ($existingEntry instanceof TaxRateEntity && $existingEntry->getId() !== $entry->getId()) {
            $this->error->add(t("The given handle is already in use."));
        }

        return !$this->error->has();
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new TaxRateEntity();

        if ($this->token->validate("save_tax_rate_entity")) {
            return $this->save($entry);
        }

        $this->setDefaults($entry);
    }

    private function setVariantDefaults($entry)
    {

        $this->set("variant", $entry);
        $this->render("/dashboard/bitter_shop_system/tax_rates/edit_variant");
    }

    public function validateVariant($data = null, $entry = null)
    {
        if (!isset($data["country"]) || empty($data["country"])) {
            $this->error->add(t("The field \"Country\" is required."));
        }

        return !$this->error->has();
    }

    /**
     * @param TaxRateVariant $entry
     * @return \Concrete\Core\Routing\RedirectResponse|Response
     */
    private function saveVariant($entry)
    {
        $data = $this->request->request->all();

        if ($this->validateVariant($data, $entry)) {
            $entry->setState((string)$data["state_province"]);
            $entry->setCountry($data["country"]);
            $entry->setRate($data["variantRate"]);

            $this->entityManager->persist($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/tax_rates/edit", $entry->getTaxRate()->getId(), "variant_updated"), Response::HTTP_TEMPORARY_REDIRECT);
        }

        $this->setVariantDefaults($entry);
    }

    public function remove_variant($variantId = null)
    {
        /** @var TaxRateVariant $entry */
        $entry = $this->entityManager->getRepository(TaxRateVariant::class)->findOneBy([
            "id" => $variantId
        ]);

        if ($entry instanceof TaxRateVariant) {
            $taxRateId = $entry->getTaxRate()->getId();

            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/tax_rates/edit", $taxRateId, "variant_removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function edit_variant($variantId = null)
    {
        /** @var TaxRateVariant $entry */
        $entry = $this->entityManager->getRepository(TaxRateVariant::class)->findOneBy([
            "id" => $variantId
        ]);

        if ($entry instanceof TaxRateVariant) {
            if ($this->token->validate("save_tax_rate_variant")) {
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
        /** @var TaxRateEntity $entry */
        $entry = $this->entityManager->getRepository(TaxRateEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof TaxRateEntity) {
            $variantEntry = new TaxRateVariant();
            $variantEntry->setTaxRate($entry);

            if ($this->token->validate("save_tax_rate_variant")) {
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
        /** @var TaxRateEntity $entry */
        $entry = $this->entityManager->getRepository(TaxRateEntity::class)->findOneBy([
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

        if ($entry instanceof TaxRateEntity) {
            if ($this->token->validate("save_tax_rate_entity")) {
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
        /** @var TaxRateEntity $entry */
        $entry = $this->entityManager->getRepository(TaxRateEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof TaxRateEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/tax_rates/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\TaxRates\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\TaxRates $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\TaxRates::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
