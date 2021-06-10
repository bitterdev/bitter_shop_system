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

class ValidFromField extends AbstractField
{
    protected $requestVariables = [
        'validFrom_from_dt',
        'validFrom_from_h',
        'validFrom_from_m',
        'validFrom_from_a',
        'validFrom_to_dt',
        'validFrom_to_h',
        'validFrom_to_m',
        'validFrom_to_a'
    ];

    public function getKey()
    {
        return 'validFrom';
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

        $dateFrom = $dateTime->translate('validFrom_from', $this->data);

        if ($dateFrom) {
            $list->filterByValidFrom($dateFrom, '>=');
        }

        $dateTo = $dateTime->translate('validFrom_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByValidFrom($dateTo, '<=');
        }
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTime */
        $dateTime = $app->make(DateTime::class);
        return $dateTime->datetime('validFrom_from', $dateTime->translate('validFrom_from', $this->data)) . t('to') . $dateTime->datetime('validFrom_to', $dateTime->translate('validFrom_to', $this->data));
    }
}
