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

class Coupons extends DashboardPageController
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
            $entry->setTaxRate($this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $data["taxRate"]]));

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
    public function edit($id = null)
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

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Coupons\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\Coupons $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\Coupons::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
