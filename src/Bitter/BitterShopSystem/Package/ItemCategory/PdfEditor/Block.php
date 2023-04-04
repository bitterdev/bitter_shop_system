<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Package\ItemCategory\PdfEditor;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block as BlockEntity;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\AbstractCategory;
use Doctrine\ORM\EntityManagerInterface;

class Block extends AbstractCategory
{
    protected $entityManager;
    protected $service;

    public function __construct(
        BlockService $service,
        EntityManagerInterface $entityManager
    )
    {
        $this->service = $service;
        $this->entityManager = $entityManager;
    }

    public function getItemCategoryDisplayName(): string
    {
        return t('Pdf Editor Blocks');
    }

    /**
     * @param BlockEntity $item
     */
    public function removeItem($item)
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /**
     * @param BlockEntity $mixed
     */
    public function getItemName($mixed): string
    {
        return t("Block %s", $mixed->getId());
    }

    public function getPackageItems(Package $package): array
    {
        return $this->service->getListByPackage($package);
    }
}