<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Order;

use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Events\PaymentReceived;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderService
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
     * @return object|Order|null
     */
    public function getById(
        int $id
    ): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->findOneBy(["id" => $id]);
    }

    /**
     * @return Order[]
     */
    public function getByCurrentUser(): array
    {
        $user = new \Concrete\Core\User\User();

        if ($user->isRegistered()) {
            $app = Application::getFacadeApplication();
            /** @var \Bitter\BitterShopSystem\Customer\CustomerService $customerService */
            $customerService = $app->make(\Bitter\BitterShopSystem\Customer\CustomerService::class);
            $userEntity = $user->getUserInfoObject()->getEntityObject();
            $customer = $customerService->getByUserEntity($userEntity);
            if ($customer instanceof Customer) {
                return $this->getByCustomer($customer);
            }
        }

        return [];
    }

    /**
     * @param Customer $customer
     * @return Order[]
     */
    public function getByCustomer(
        Customer $customer
    ): array
    {
        return $this->entityManager->getRepository(Order::class)->findBy(["customer" => $customer]);
    }

    /**
     * @param string $transactionId
     * @return object|Order|null
     */
    public function getByTransactionId(
        string $transactionId
    ): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->findOneBy(["transactionId" => $transactionId]);
    }

    /**
     * @return Order[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Order::class)->findAll();
    }

    public function markOrderAsPaid(Order $order)
    {
        $order->setPaymentReceived(true);
        $order->setPaymentReceivedDate(new \DateTime());
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $event = new PaymentReceived();
        $event->setOrder($order);
        $this->eventDispatcher->dispatch($event, "on_payment_received");
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Order::class)->findBy(["package" => $pkg]);
    }
}