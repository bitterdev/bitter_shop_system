<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

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
use Concrete\Core\Page\Page;
use Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PaymentProviders\OnlineBankTransfer as Configuration;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Url;
use Sofort\SofortLib\Notification;
use Sofort\SofortLib\Sofortueberweisung;
use Sofort\SofortLib\TransactionData;

class OnlineBankTransfer extends PaymentProvider implements PaymentProviderInterface
{
    public function getName(): string
    {
        return t("Online Bank Transfer");
    }

    public function getHandle(): string
    {
        return "online_bank_transfer";
    }

    public function processPaymentNotification(): void
    {
        $sofortLibNotification = new Notification();

        $orderNotification = $sofortLibNotification->getNotification(file_get_contents('php://input'));

        $transactionData = new TransactionData($this->config->get("bitter_shop_system.payment_providers.online_bank_transfer.config_key"));

        $transactionData->addTransaction($sofortLibNotification->getTransactionId());
        $transactionData->setApiVersion('2.0');
        $transactionData->sendRequest();

        $paymentStatus = $transactionData->getStatus();

        $order = $this->orderService->getByTransactionId($sofortLibNotification->getTransactionId());

        if ($order instanceof Order) {
            switch ($paymentStatus) {
                case "untraceable":
                case "received":
                case "approved":
                    $this->orderService->markOrderAsPaid($order);
                    break;

                default:
                    $event = new PaymentFailed();
                    $event->setOrder($order);
                    $this->eventDispatcher->dispatch("on_payment_failed", $event);
                    break;
            }
        }
    }

    public function processPayment(): void
    {
        $sofortBanking = new Sofortueberweisung($this->config->get("bitter_shop_system.payment_providers.online_bank_transfer.config_key"));
        $sofortBanking->setVersion(sprintf("BitterShopSystem_%s/Sofort_%s", $this->pkg->getPackageVersion(), SOFORTLIB_VERSION));
        $sofortBanking->setAmount($this->checkoutService->getTotal());
        $sofortBanking->setCurrencyCode($this->config->get("bitter_shop_system.money_formatting.currency_code", "USD"));
        $sofortBanking->setReason(t("Order"));
        $sofortBanking->setSuccessUrl((string)Url::to($this->checkoutService->getCheckoutPage(), "complete"), true);
        $sofortBanking->setAbortUrl((string)Url::to($this->checkoutService->getCheckoutPage(), "payment_failed"));
        $sofortBanking->setNotificationUrl((string)Url::to("/api/v1/payments/process_payment/online_bank_transfer"));

        $sofortBanking->sendRequest();

        if ($sofortBanking->isError()) {
            $this->logger->error($sofortBanking->getError());
            $this->responseFactory->redirect((string)Url::to($this->checkoutService->getCheckoutPage(), "payment_failed"), Response::HTTP_TEMPORARY_REDIRECT)->send();
        } else {
            $order = $this->checkoutService->transformToRealOrder();
            $order->setTransactionId($sofortBanking->getTransactionId());
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->responseFactory->redirect($sofortBanking->getPaymentUrl(), Response::HTTP_TEMPORARY_REDIRECT)->send();
        }

        $this->app->shutdown();
    }

    public function getConfigurationElement(): ?PaymentConfigurationInterface
    {
        return $this->app->make(Configuration::class);
    }
}
