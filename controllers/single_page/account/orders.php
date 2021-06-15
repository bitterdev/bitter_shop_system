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
use Bitter\BitterShopSystem\OrderConfirmation\OrderConfirmationService;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;

class Orders extends AccountPageController
{
    public function download($orderId = null)
    {
        /** @var OrderService $orderService */
        $orderService = $this->app->make(OrderService::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var OrderConfirmationService $orderConfirmationService */
        $orderConfirmationService = $this->app->make(OrderConfirmationService::class);
        $order = $orderService->getById((int)$orderId);
        $user = new \Concrete\Core\User\User();

        if ($order instanceof Order) {
            $customer = $order->getCustomer();
            if ($customer instanceof Customer) {
                $userEntity = $customer->getUser();

                if ($userEntity instanceof User) {
                    if ($user->getUserID() == $userEntity->getUserID()) {
                        $pdfData = $orderConfirmationService->createPdfOrderConfirmation($order)->Output("S");

                        $response = new Response();
                        $response->headers->set('Cache-Control', 'private');
                        $response->headers->set('Content-type', "application/pdf");
                        $response->headers->set('Content-length', strlen($pdfData));
                        $response->sendHeaders();
                        $response->setContent($pdfData);
                        return $response;
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
