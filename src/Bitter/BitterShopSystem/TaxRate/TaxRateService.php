<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\TaxRate;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class TaxRateService
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
     * @return object|TaxRate|null
     */
    public function getById(
        int $id
    ): ?TaxRate
    {
        return $this->entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $handle
     * @return object|TaxRate|null
     */
    public function getByHandle(
        string $handle
    ): ?TaxRate
    {
        return $this->entityManager->getRepository(TaxRate::class)->findOneBy(["handle" => $handle]);
    }

    /**
     * @return TaxRate[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(TaxRate::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(TaxRate::class)->findBy(["package" => $pkg]);
    }
}