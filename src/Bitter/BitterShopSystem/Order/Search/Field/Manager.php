<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\Field;

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\Order\Search\Field\Field\CustomerField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\OrderDateField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\PaymentProviderField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\PaymentReceivedDateField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\PaymentReceivedField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\SubtotalField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\TaxField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\TotalField;
use Bitter\BitterShopSystem\Order\Search\Field\Field\TransactionIdField;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\Support\Facade\Application;
use DateTime;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

class Manager extends FieldManager
{

    public function getPaymentReceivedState(Order $order): string
    {
        return $order->isPaymentReceived() ? t("Yes") : t("No");
    }

    public function getTax(Order $order): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($order->getTax());
    }

    public function getTotal(Order $order): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($order->getTotal());
    }

    public function getSubtotal(Order $order): string
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($order->getSubtotal());
    }

    public function getCustomerName(Order $order): string
    {
        if ($order->getCustomer() instanceof Customer) {
            if ($order->getCustomer()->getUser() instanceof User) {
                return $order->getCustomer()->getUser()->getUserName();
            } else {
                return $order->getCustomer()->getEmail();
            }
        } else {
            return '';
        }
    }

    public function getPaymentProviderName(Order $order): string
    {
        return $order->getPaymentProvider() instanceof PaymentProviderInterface ? $order->getPaymentProvider()->getName() : "";
    }

    public function getPaymentReceivedDate(Order $order): string
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);

        if ($order->getPaymentReceivedDate() instanceof DateTime) {
            try {
                return $dateService->formatDateTime($order->getPaymentReceivedDate());
            } catch (BadArgumentType | Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    public function getOrderDate(Order $order): string
    {
        $app = Application::getFacadeApplication();
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);

        if ($order->getOrderDate() instanceof DateTime) {
            try {
                return $dateService->formatDateTime($order->getOrderDate());
            } catch (BadArgumentType | Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    public function __construct()
    {
        $properties = [
            new CustomerField(),
            new OrderDateField(),
            new PaymentProviderField(),
            new PaymentReceivedDateField(),
            new PaymentReceivedField(),
            new SubtotalField(),
            new TaxField(),
            new TotalField(),
            new TransactionIdField()
        ];

        $this->addGroup(t('Core Properties'), $properties);
    }
}
