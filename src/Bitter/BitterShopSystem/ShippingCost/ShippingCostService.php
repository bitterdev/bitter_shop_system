<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\ShippingCost;

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class ShippingCostService
{
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return object|ShippingCost|null
     */
    public function getById(
        int $id
    ): ?ShippingCost
    {
        return $this->entityManager->getRepository(ShippingCost::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $handle
     * @return object|ShippingCost|null
     */
    public function getByHandle(
        string $handle
    ): ?ShippingCost
    {
        return $this->entityManager->getRepository(ShippingCost::class)->findOneBy(["handle" => $handle]);
    }

    /**
     * @return ShippingCost[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(ShippingCost::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(ShippingCost::class)->findBy(["package" => $pkg]);
    }
}