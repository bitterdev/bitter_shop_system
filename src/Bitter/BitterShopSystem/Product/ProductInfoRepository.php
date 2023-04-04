<?php /** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Product;

use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductInfoRepository
{
    protected $entityManager;
    protected $application;
    protected $repository;

    public function __construct(
        Application $application,
        EntityManagerInterface $entityManager
    )
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    protected function getRepository(): ObjectRepository
    {
        if (!$this->repository) {
            $this->repository = $this->entityManager->getRepository(Product::class);
        }

        return $this->repository;
    }

    public function getById($id): ?ProductInfo
    {
        return $this->get('id', $id);
    }

    /**
     * @param $where
     * @param $var
     * @return ProductInfo|null
     */
    private function get($where, $var): ?ProductInfo
    {
        /** @var Product $entity */
        $entity = $this->getRepository()->findOneBy([$where => $var]);

        if (!is_object($entity)) {
            return null;
        }

        return $this->getByProductEntity($entity);
    }

    /**
     * @param Product $entity
     * @return ProductInfo
     */
    public function getByProductEntity(Product $entity): ProductInfo
    {
        /** @var ProductInfo $ProductInfo */
        $ProductInfo = $this->application->make(ProductInfo::class);
        $ProductInfo->setEntity($entity);
        return $ProductInfo;
    }

}