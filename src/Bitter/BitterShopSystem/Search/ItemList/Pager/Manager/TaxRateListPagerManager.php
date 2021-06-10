<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Search\ItemList\Pager\Manager;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\TaxRate\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\AbstractPagerManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

class TaxRateListPagerManager extends AbstractPagerManager
{
    /** 
     * @param TaxRate $taxRate
     * @return int 
     */
    public function getCursorStartValue($taxRate)
    {
        return $taxRate->getId();
    }
    
    public function getCursorObject($cursor)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $cursor]);
    }
    
    public function getAvailableColumnSet()
    {
        return new Available();
    }
    
    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $itemList->getQueryObject()->addOrderBy('t0.id', $direction);
    }
}
