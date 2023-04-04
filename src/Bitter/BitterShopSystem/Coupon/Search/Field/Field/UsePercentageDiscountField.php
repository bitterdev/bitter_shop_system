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
use HtmlObject\Element;

class UsePercentageDiscountField extends AbstractField
{
    protected $requestVariables = [
        'usePercentageDiscount'
    ];

    public function getKey()
    {
        return 'usePercentageDiscount';
    }

    public function getDisplayName()
    {
        return t('Use Percentage Discount');
    }

    /**
     * @param CouponList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByUsePercentageDiscount(isset($this->data['usePercentageDiscount']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $checkbox = new Element("div", new Element("label", $form->checkbox('usePercentageDiscount', 1, isset($this->data['usePercentageDiscount'])) . t("Use Percentage Discount")), ["class" => "checkbox"]);
        return (string)$checkbox->render();
    }
}
