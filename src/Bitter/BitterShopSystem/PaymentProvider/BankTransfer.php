<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

class BankTransfer extends PaymentProvider implements PaymentProviderInterface
{
    public function getName(): string
    {
        return t("Bank Transfer");
    }

    public function getHandle(): string
    {
        return "bank_transfer";
    }

    public function processPayment(): void
    {
        $this->checkoutService->transformToRealOrder();
        $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "complete"), Response::HTTP_TEMPORARY_REDIRECT)->send();
        $this->app->shutdown();
    }

    public function getConfigurationElement(): ?PaymentConfigurationInterface
    {
        return null;
    }

    public function processPaymentNotification(): void
    {

    }
}