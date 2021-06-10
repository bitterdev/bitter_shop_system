<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Category;

use Bitter\BitterShopSystem\Entity\Category;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
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
     * @return object|Category|null
     */
    public function getById(
        int $id
    ): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $handle
     * @return object|Category|null
     */
    public function getByHandle(
        string $handle
    ): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(["handle" => $handle]);
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Category::class)->findBy(["package" => $pkg]);
    }
}