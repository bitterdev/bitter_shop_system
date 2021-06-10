<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Account;

use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Order\OrderService;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

class Orders extends AccountPageController
{
    public function details($orderId = null)
    {
        /** @var OrderService $orderService */
        $orderService = $this->app->make(OrderService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $order = $orderService->getById((int)$orderId);
        $user = new \Concrete\Core\User\User();

        if ($order instanceof Order) {
            $customer = $order->getCustomer();
            if ($customer instanceof Customer) {
                $userEntity = $customer->getUser();

                if ($userEntity instanceof User) {
                    if ($user->getUserID() == $userEntity->getUserID()) {
                        $this->set("order", $order);
                        $this->render("/account/orders/details");
                        return null;
                    }
                }
            }

            return $responseFactory->forbidden((string)Url::to(Page::getCurrentPage(), "download_receipt", $orderId));
        } else {
            return $responseFactory->notFound(t("Order not found."));
        }
    }

    public function view()
    {
        /** @var OrderService $orderService */
        $orderService = $this->app->make(OrderService::class);
        $this->set('orders', $orderService->getByCurrentUser());
    }
}
