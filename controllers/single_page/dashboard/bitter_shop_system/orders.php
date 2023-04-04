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

use Bitter\BitterShopSystem\Order\OrderService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Orders\OrderDetailHeader;
use Symfony\Component\HttpFoundation\Response;
use Bitter\BitterShopSystem\Entity\Order as OrderEntity;

class Orders extends DashboardPageController
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

    public function removed()
    {
        $this->set("success", t('The item has been successfully removed.'));
        $this->view();
    }

    public function marked_as_paid($orderId = null)
    {
        $this->set("success", t('The order has been successfully marked as paid.'));
        $this->details($orderId);
    }

    public function mark_as_paid($orderId = null)
    {
        /** @var OrderEntity $entry */
        $entry = $this->entityManager->getRepository(OrderEntity::class)->findOneBy([
            "id" => $orderId
        ]);

        if ($entry instanceof OrderEntity) {
            /** @var OrderService $orderService */
            $orderService = $this->app->make(OrderService::class);
            $orderService->markOrderAsPaid($entry);
            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/orders/marked_as_paid", $orderId), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function details($id = null)
    {
        /** @var OrderEntity $entry */
        $entry = $this->entityManager->getRepository(OrderEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof OrderEntity) {
            $headerMenu = new OrderDetailHeader();
            $headerMenu->set("entry", $entry);
            $this->set("entry", $entry);
            $this->set("headerMenu", $headerMenu);
            $this->render("/dashboard/bitter_shop_system/orders/details");
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
        /** @var OrderEntity $entry */
        $entry = $this->entityManager->getRepository(OrderEntity::class)->findOneBy([
            "id" => $id
        ]);

        if ($entry instanceof OrderEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/bitter_shop_system/orders/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    public function view()
    {
        $headerMenu = new \Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Orders\SearchHeader();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\BitterShopSystem\Controller\Search\Orders $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Search\Orders::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
