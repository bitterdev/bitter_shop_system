<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\TaxRate\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\TaxRate\TaxRateList;

class RateField extends AbstractField
{
    protected $requestVariables = [
        'rate'
    ];
    
    public function getKey()
    {
        return 'rate';
    }
    
    public function getDisplayName()
    {
        return t('Rate');
    }
    
    /**
     * @param TaxRateList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterByRate($this->data['rate']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->number('rate', $this->data['rate']);
    }
}
