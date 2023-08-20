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
use Bitter\BitterShopSystem\Entity\Category;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Product\ProductList;

class CategoryField extends AbstractField
{
    protected $requestVariables = [
        'category'
    ];
    
    public function getKey()
    {
        return 'category';
    }
    
    public function getDisplayName()
    {
        return t('Category');
    }
    
    /**
     * @param ProductList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByCategory(@$this->data['category']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Form $form */
        $form = $app->make(Form::class);
        
        $entries = [];
        
        foreach ($entityManager->getRepository(Category::class)->findAll() as $entry) {
            /** @var Category $entry */
            $entries[$entry->getId()] = $entry->getName();
        }
        
        return $form->select('category', $entries, @$this->data['category']);
    }
}
