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

class LimitQuantityField extends AbstractField
{
    protected $requestVariables = [
        'limitQuantity'
    ];

    public function getKey()
    {
        return 'limitQuantity';
    }

    public function getDisplayName()
    {
        return t('Limit Quantity');
    }

    /**
     * @param CouponList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByLimitQuantity(isset($this->data['limitQuantity']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $checkbox = new Element("div", new Element("label", $form->checkbox('limitQuantity', 1, isset($this->data['limitQuantity'])) . t("Limit Quantity")), ["class" => "checkbox"]);
        return (string)$checkbox->render();
    }
}
