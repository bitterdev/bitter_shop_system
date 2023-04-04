<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\ShippingCost\Search\Field\Field;

use Doctrine\ORM\EntityManagerInterface;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostList;

class TaxRateField extends AbstractField
{
    protected $requestVariables = [
        'taxRate'
    ];
    
    public function getKey()
    {
        return 'taxRate';
    }
    
    public function getDisplayName()
    {
        return t('Tax Rate');
    }
    
    /**
     * @param ShippingCostList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByTaxRate($this->data['taxRate']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Form $form */
        $form = $app->make(Form::class);
        
        $entries = [];
        
        foreach ($entityManager->getRepository(TaxRate::class)->findAll() as $entry) {
            /** @var TaxRate $entry */
            $entries[$entry->getId()] = $entry->getName();
        }
        
        return $form->select('taxRate', $entries, $this->data['taxRate']);
    }
}
