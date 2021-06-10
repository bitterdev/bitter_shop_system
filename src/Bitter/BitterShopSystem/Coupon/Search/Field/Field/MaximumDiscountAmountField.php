<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Coupon\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Coupon\CouponList;

class MaximumDiscountAmountField extends AbstractField
{
    protected $requestVariables = [
        'maximumDiscountAmount'
    ];

    public function getKey()
    {
        return 'maximumDiscountAmount';
    }

    public function getDisplayName()
    {
        return t('Maximum Discount Amount');
    }

    /**
     * @param CouponList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByMaximumDiscountAmount($this->data['maximumDiscountAmount']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('maximumDiscountAmount', $this->data['maximumDiscountAmount']);
    }
}
