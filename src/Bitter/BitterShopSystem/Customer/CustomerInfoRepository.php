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

namespace Bitter\BitterShopSystem\Customer;

use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class CustomerInfoRepository
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
            $this->repository = $this->entityManager->getRepository(Customer::class);
        }

        return $this->repository;
    }

    public function getById($id): ?CustomerInfo
    {
        return $this->get('id', $id);
    }

    /**
     * @param $where
     * @param $var
     * @return CustomerInfo|null
     */
    private function get($where, $var): ?CustomerInfo
    {
        /** @var Customer $entity */
        $entity = $this->getRepository()->findOneBy([$where => $var]);

        if (!is_object($entity)) {
            return null;
        }

        return $this->getByCustomerEntity($entity);
    }

    /**
     * @param Customer $entity
     * @return CustomerInfo
     */
    public function getByCustomerEntity(Customer $entity): CustomerInfo
    {
        /** @var CustomerInfo $CustomerInfo */
        $CustomerInfo = $this->application->make(CustomerInfo::class);
        $CustomerInfo->setEntity($entity);
        return $CustomerInfo;
    }

}