<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Order\OrderList;

class PaymentReceivedDateField extends AbstractField
{
    protected $requestVariables = [
        'paymentReceivedDate_from_dt',
        'paymentReceivedDate_from_h',
        'paymentReceivedDate_from_m',
        'paymentReceivedDate_from_a',
        'paymentReceivedDate_to_dt',
        'paymentReceivedDate_to_h',
        'paymentReceivedDate_to_m',
        'paymentReceivedDate_to_a'
    ];

    public function getKey()
    {
        return 'paymentReceivedDate';
    }

    public function getDisplayName()
    {
        return t('Payment Received Date');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);

        $dateFrom = $dateTime->translate('paymentReceivedDate_from', $this->data);

        if ($dateFrom) {
            $list->filterByPaymentReceivedDate($dateFrom, '>=');
        }

        $dateTo = $dateTime->translate('paymentReceivedDate_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByPaymentReceivedDate($dateTo, '<=');
        }
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        return $dateTime->datetime('paymentReceivedDate_from', $dateTime->translate('paymentReceivedDate_from', $this->data)) . t('to') . $dateTime->datetime('paymentReceivedDate_to', $dateTime->translate('paymentReceivedDate_to', $this->data));
    }
}
