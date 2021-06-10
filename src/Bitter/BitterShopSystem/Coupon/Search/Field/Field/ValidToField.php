<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Coupon\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Coupon\CouponList;

class ValidToField extends AbstractField
{
    protected $requestVariables = [
        'validTo_from_dt',
        'validTo_from_h',
        'validTo_from_m',
        'validTo_from_a',
        'validTo_to_dt',
        'validTo_to_h',
        'validTo_to_m',
        'validTo_to_a'
    ];

    public function getKey()
    {
        return 'validTo';
    }

    public function getDisplayName()
    {
        return t('Order Date');
    }

    /**
     * @param CouponList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);

        $dateFrom = $dateTime->translate('validTo_from', $this->data);

        if ($dateFrom) {
            $list->filterByValidTo($dateFrom, '>=');
        }

        $dateTo = $dateTime->translate('validTo_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByValidTo($dateTo, '<=');
        }
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        return $dateTime->datetime('validTo_from', $dateTime->translate('validTo_from', $this->data)) . t('to') . $dateTime->datetime('validTo_to', $dateTime->translate('validTo_to', $this->data));
    }
}
