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

class OrderDateField extends AbstractField
{
    protected $requestVariables = [
        'orderDate_from_dt',
        'orderDate_from_h',
        'orderDate_from_m',
        'orderDate_from_a',
        'orderDate_to_dt',
        'orderDate_to_h',
        'orderDate_to_m',
        'orderDate_to_a'
    ];

    public function getKey()
    {
        return 'orderDate';
    }

    public function getDisplayName()
    {
        return t('Order Date');
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

        $dateFrom = $dateTime->translate('orderDate_from', $this->data);

        if ($dateFrom) {
            $list->filterByOrderDate($dateFrom, '>=');
        }

        $dateTo = $dateTime->translate('orderDate_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByOrderDate($dateTo, '<=');
        }
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        return $dateTime->datetime('orderDate_from', $dateTime->translate('orderDate_from', $this->data)) . t('to') . $dateTime->datetime('orderDate_to', $dateTime->translate('orderDate_to', $this->data));
    }
}
