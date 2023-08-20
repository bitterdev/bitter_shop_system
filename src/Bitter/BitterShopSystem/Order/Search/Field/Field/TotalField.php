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

class TotalField extends AbstractField
{
    protected $requestVariables = [
        'totalFrom',
        'totalTo'
    ];

    public function getKey()
    {
        return 'total';
    }

    public function getDisplayName()
    {
        return t('Total');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByTotal((int)@$this->data['totalFrom'], (int)@$this->data['totalTo']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->number('totalFrom', @$this->data['totalFrom']) . t("to") . $form->number('totalTo', @$this->data['totalTo']);
    }
}
