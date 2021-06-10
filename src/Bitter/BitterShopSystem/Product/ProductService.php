<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Product;

use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Core\Multilingual\Page\Section\Section;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Package;

class ProductService
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
     * @return object|Product|null
     */
    public function getById(
        int $id
    ): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $handle
     * @return object|Product|null
     */
    public function getByHandleWithCurrentLocale(
        string $handle
    ): ?Product
    {
        return $this->getByHandleWithLocale($handle, Section::getCurrentSection()->getLocale());
    }

    /**
     * @param string $handle
     * @param string $locale
     * @return object|Product|null
     */
    public function getByHandleWithLocale(
        string $handle,
        string $locale
    ): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy([
            "handle" => $handle,
            "locale" => $locale
        ]);
    }

    /**
     * @param string $handle
     * @return object|Product|null
     */
    public function getByHandle(
        string $handle
    ): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy(["handle" => $handle]);
    }

    /**
     * @param string $locale
     * @return object[]
     */
    public function getAllByLocale(string $locale): array
    {
        return $this->entityManager->getRepository(Product::class)->findBy(["locale" => $locale]);
    }

    /**
     * @return object[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Product::class)->findBy(["package" => $pkg]);
    }
}