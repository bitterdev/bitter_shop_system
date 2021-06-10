<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Package\ItemCategory;

use Bitter\BitterShopSystem\Entity\Category as CategoryEntity;
use Bitter\BitterShopSystem\Category\CategoryService;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\AbstractCategory;
use Doctrine\ORM\EntityManagerInterface;

class Category extends AbstractCategory
{
    protected $entityManager;
    protected $service;

    public function __construct(
        CategoryService $service,
        EntityManagerInterface $entityManager
    )
    {
        $this->service = $service;
        $this->entityManager = $entityManager;
    }

    public function getItemCategoryDisplayName(): string
    {
        return t('Tax Rates');
    }

    /**
     * @param CategoryEntity $item
     */
    public function removeItem($item)
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /**
     * @param CategoryEntity $mixed
     */
    public function getItemName($mixed): string
    {
        return $mixed->getName();
    }

    public function getPackageItems(Package $package): array
    {
        return $this->service->getListByPackage($package);
    }
}