<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\Field\Field;

use Bitter\BitterShopSystem\Customer\CustomerList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class EmailField extends AbstractField
{
    protected $requestVariables = [
        'email'
    ];

    public function getKey()
    {
        return 'email';
    }

    public function getDisplayName()
    {
        return t('Email');
    }

    /**
     * @param CustomerList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByKeywords((int)@$this->data['email']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->email('email', @$this->data['email']);
    }
}
