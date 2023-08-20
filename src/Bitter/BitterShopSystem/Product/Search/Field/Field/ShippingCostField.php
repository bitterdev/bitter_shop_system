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

use Doctrine\ORM\EntityManagerInterface;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Product\ProductList;

class ShippingCostField extends AbstractField
{
    protected $requestVariables = [
        'shippingCost'
    ];
    
    public function getKey()
    {
        return 'shippingCost';
    }
    
    public function getDisplayName()
    {
        return t('Shipping Cost');
    }
    
    /**
     * @param ProductList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByShippingCost(@$this->data['shippingCost']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Form $form */
        $form = $app->make(Form::class);
        
        $entries = [];
        
        foreach ($entityManager->getRepository(ShippingCost::class)->findAll() as $entry) {
            /** @var ShippingCost $entry */
            $entries[$entry->getId()] = $entry->getName();
        }
        
        return $form->select('shippingCost', $entries, @$this->data['shippingCost']);
    }
}
