<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Dashboard\BitterShopSystem\Settings;

use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Entity\OrderPosition;
use Bitter\BitterShopSystem\OrderConfirmation\OrderConfirmationService;
use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\Settings\OrderConfirmation\Header;

class OrderConfirmation extends DashboardPageController
{
    public function preview()
    {
        /** @var OrderConfirmationService $orderConfirmationService */
        $orderConfirmationService = $this->app->make(OrderConfirmationService::class);
        $dummyOrder = new Order();
        $orderPositions = [];
        $tax = 0;
        $subtotal = 0;

        $customer = new Customer();

        for ($i = 1; $i <= 5; $i++) {
            $itemPrice = 100;
            $itemTax = 10;
            $orderPosition = new OrderPosition();
            $orderPosition->setQuantity(1);
            $orderPosition->setDescription(t("Dummy Article"));
            $orderPosition->setTax($itemTax);
            $orderPosition->setPrice($itemPrice);

            $orderPositions[] = $orderPosition;

            $subtotal += $itemPrice;
            $tax += $itemTax;
        }

        $total = $subtotal + $tax;

        $dummyOrder->setOrderPositions($orderPositions);
        $dummyOrder->setOrderDate(new \DateTime());
        $dummyOrder->setTotal($total);
        $dummyOrder->setTax($tax);
        $dummyOrder->setSubtotal($subtotal);
        $dummyOrder->setCustomer($customer);

        $document = $orderConfirmationService->createPdfOrderConfirmation($dummyOrder);

        $pdfData = $document->Output("S");

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', "application/pdf");
        $response->headers->set('Content-length', strlen($pdfData));
        $response->sendHeaders();
        $response->setContent($pdfData);
        return $response;
    }

    public function view()
    {
        $this->requireAsset("bitter_shop_system/pdf_editor");
        $this->set('headerMenu', new Header());
    }
}