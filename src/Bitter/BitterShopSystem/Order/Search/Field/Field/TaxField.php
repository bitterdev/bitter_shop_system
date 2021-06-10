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

class TaxField extends AbstractField
{
    protected $requestVariables = [
        'taxFrom',
        'taxTo'
    ];

    public function getKey()
    {
        return 'tax';
    }

    public function getDisplayName()
    {
        return t('Tax');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByTax((int)$this->data['taxFrom'], (int)$this->data['taxTo']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->number('taxFrom', $this->data['taxFrom']) . t("to") . $form->number('taxTo', $this->data['taxTo']);
    }
}
