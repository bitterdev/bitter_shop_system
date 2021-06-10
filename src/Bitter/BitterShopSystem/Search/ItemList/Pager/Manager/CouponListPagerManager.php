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

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Coupon\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\AbstractPagerManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

class CouponListPagerManager extends AbstractPagerManager
{
    /**
     * @param Coupon $coupon
     * @return int
     */
    public function getCursorStartValue($coupon)
    {
        return $coupon->getId();
    }

    public function getCursorObject($cursor)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(Coupon::class)->findOneBy(["id" => $cursor]);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $itemList->getQueryObject()->addOrderBy('t5.id', $direction);
    }
}
