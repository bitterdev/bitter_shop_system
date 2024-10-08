<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Product\ProductList;

class SortOrderField extends AbstractField
{
    protected $requestVariables = [
        'sortOrder'
    ];

    public function getKey()
    {
        return 'sortOrder';
    }

    public function getDisplayName()
    {
        return t('Sort Order');
    }

    /**
     * @param ProductList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterBySortOrder((int)@$this->data['sortOrder']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('sortOrder', @$this->data['sortOrder']);
    }
}
