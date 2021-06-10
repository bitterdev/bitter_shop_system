<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Customer;

use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomerService
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
     * @return object|Customer|null
     */
    public function getById(
        int $id
    ): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $mailAddress
     * @return object|Customer|null
     */
    public function getByMailAddress(
        string $mailAddress
    ): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->findOneBy(["email" => $mailAddress]);
    }

    /**
     * @param User $userEntity
     * @return object|Customer|null
     */
    public function getByUserEntity(
        User $userEntity
    ): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->findOneBy(["user" => $userEntity]);
    }

    /**
     * @return array|Customer[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Customer::class)->findAll();
    }

    public function getList(): array
    {
        $listItems = [];

        foreach ($this->getAll() as $customer) {
            $listItems[$customer->getId()] = $customer->getEmail();
        }

        return $listItems;
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Customer::class)->findBy(["package" => $pkg]);
    }
}