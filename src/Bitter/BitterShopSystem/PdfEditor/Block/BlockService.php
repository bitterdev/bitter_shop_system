<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlockService
{
    protected $entityManager;
    protected $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $id
     * @return object|Block|null
     */
    public function getById(
        int $id
    ): ?Block
    {
        return $this->entityManager->getRepository(Block::class)->findOneBy(["id" => $id]);
    }

    /**
     * @return array|Block[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Block::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Block::class)->findBy(["package" => $pkg]);
    }
}