<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Events\PaymentFailed;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PaymentProviders\Paypal as Configuration;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Url;
use Exception;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal extends PaymentProvider implements PaymentProviderInterface
{
    /** @var ApiContext */
    protected $apiContext;

    public function getName(): string
    {
        return t("PayPal");
    }

    public function getHandle(): string
    {
        return "paypal";
    }

    public function processPaymentNotification(): void
    {
        $paymentId = $this->request->query->get("paymentId");
        $payerId = $this->request->query->get("PayerID");
        $orderId = (int)$this->request->query->get("orderId");

        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();

        $execution->setPayerId($payerId);

        try {
            $payment->execute($execution, $this->apiContext);

            $paymentStatus = $payment->getState();

            $orderEntry = $this->orderService->getById($orderId);

            if ($orderEntry instanceof Order) {
                if ($paymentStatus === "approved") {
                    $this->orderService->markOrderAsPaid($orderEntry);
                } else {
                    $event = new PaymentFailed();
                    $event->setOrder($orderEntry);
                    $this->eventDispatcher->dispatch($event, "on_payment_failed");
                }
            }

            $this->responseFactory->redirect((string)Url::to($this->checkoutService->getCheckoutPage(), "complete"), Response::HTTP_TEMPORARY_REDIRECT)->send();
            $this->app->shutdown();

        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());

            $this->responseFactory->redirect((string)Url::to($this->checkoutService->getCheckoutPage(), "payment_failed"), Response::HTTP_TEMPORARY_REDIRECT)->send();
            $this->app->shutdown();
        }
    }

    public function on_start(): void
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->config->get("bitter_shop_system.payment_providers.paypal.client_id"),
                $this->config->get("bitter_shop_system.payment_providers.paypal.client_secret")
            )
        );

        if ($this->config->get("bitter_shop_system.payment_providers.paypal.selected_mode") === "production") {
            $this->apiContext->setConfig(["mode" => "live"]);
        }
    }

    public function processPayment(): void
    {
        $order = $this->checkoutService->transformToRealOrder();

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $items = [];

        foreach ($order->getOrderPositions() as $orderPosition) {
            $item = new Item();

            $item
                ->setName($orderPosition->getDescription())
                ->setCurrency($this->config->get("bitter_shop_system.money_formatting.currency_code", "USD"))
                ->setQuantity($orderPosition->getQuantity())
                ->setPrice($orderPosition->getPrice() / $orderPosition->getQuantity());

            $items[] = $item;
        }

        $itemList = new ItemList();
        $itemList->setItems($items);

        $details = new Details();
        $details
            ->setTax($order->getTax())
            ->setSubtotal($order->getSubtotal());

        $amount = new Amount();
        $amount
            ->setCurrency($this->config->get("bitter_shop_system.money_formatting.currency_code", "USD"))
            ->setTotal($order->getTotal())
            ->setDetails($details);

        $transaction = new Transaction();

        $transaction
            ->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription(t("Order %s", $order->getId()))
            ->setInvoiceNumber($order->getId());

        $redirectUrls = new RedirectUrls();
        $redirectUrls
            ->setReturnUrl((string)Url::to("/api/v1/payments/process_payment/paypal")->setQuery([
                "orderId" => $order->getId()
            ]))
            ->setCancelUrl((string)Url::to($this->checkoutService->getCheckoutPage(), "payment_failed"));

        $payment = new Payment();

        $payment
            ->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $payment->create($this->apiContext);

        $this->responseFactory->redirect($payment->getApprovalLink(), Response::HTTP_TEMPORARY_REDIRECT)->send();
        $this->app->shutdown();
    }

    public function getConfigurationElement(): ?PaymentConfigurationInterface
    {
        return $this->app->make(Configuration::class);
    }
}
