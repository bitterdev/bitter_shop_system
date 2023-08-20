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
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Order\OrderList;

class SubtotalField extends AbstractField
{
    protected $requestVariables = [
        'subtotalFrom',
        'subtotalTo'
    ];

    public function getKey()
    {
        return 'subtotal';
    }

    public function getDisplayName()
    {
        return t('Subtotal');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterBySubtotal((int)@$this->data['subtotalTo'], (int)@$this->data['subtotalTo']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->number('subtotalFrom', @$this->data['subtotalFrom']) . t("to") . $form->number('subtotalTo', @$this->data['subtotalTo']);
    }
}
