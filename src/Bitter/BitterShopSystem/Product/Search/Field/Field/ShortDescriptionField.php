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

class ShortDescriptionField extends AbstractField
{
    protected $requestVariables = [
        'shortDescription'
    ];
    
    public function getKey()
    {
        return 'shortDescription';
    }
    
    public function getDisplayName()
    {
        return t('Short Description');
    }
    
    /**
     * @param ProductList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByShortDescription($this->data['shortDescription']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->textarea('shortDescription', $this->data['shortDescription']);
    }
}
