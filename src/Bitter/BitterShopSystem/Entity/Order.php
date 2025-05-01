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

namespace Bitter\BitterShopSystem\Entity;

use Bitter\BitterShopSystem\Entity\Notification\OrderCreatedNotification;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="`Order`")
 */
class Order implements ExportableInterface, SubjectInterface
{
    use PackageTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $orderDate;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $paymentReceivedDate;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $paymentReceived = false;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $paymentProviderHandle;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $subtotal;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $tax;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=14, scale=4)
     */
    protected $total;

    /**
     * @var Collection|OrderPosition[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\OrderPosition", mappedBy="order")
     */
    protected $orderPositions;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $transactionId;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(name="customerId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @var Collection|OrderCreatedNotification[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\Notification\OrderCreatedNotification", mappedBy="order", cascade={"remove"}),
     */
    protected $notifications;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Order
     */
    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaymentReceived(): bool
    {
        return $this->paymentReceived;
    }

    /**
     * @param bool $paymentReceived
     * @return Order
     */
    public function setPaymentReceived(bool $paymentReceived): Order
    {
        $this->paymentReceived = $paymentReceived;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentProviderHandle(): string
    {
        return $this->paymentProviderHandle;
    }

    /**
     * @return PaymentProviderInterface|null
     */
    public function getPaymentProvider(): ?PaymentProviderInterface
    {
        $app = Application::getFacadeApplication();
        /** @var PaymentProviderService $paymentProviderService */
        $paymentProviderService = $app->make(PaymentProviderService::class);
        return $paymentProviderService->getByHandle($this->getPaymentProviderHandle());
    }

    /**
     * @param string $paymentProviderHandle
     * @return Order
     */
    public function setPaymentProviderHandle(string $paymentProviderHandle): Order
    {
        $this->paymentProviderHandle = $paymentProviderHandle;
        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    /**
     * @param float $subtotal
     * @return Order
     */
    public function setSubtotal(float $subtotal): Order
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     * @return Order
     */
    public function setTax(float $tax): Order
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return Order
     */
    public function setTotal(float $total): Order
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return OrderPosition[]|PersistentCollection
     */
    public function getOrderPositions()
    {
        return $this->orderPositions;
    }

    /**
     * @param OrderPosition[] $orderPositions
     * @return Order
     */
    public function setOrderPositions(array $orderPositions): Order
    {
        $this->orderPositions = $orderPositions;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return Order
     */
    public function setTransactionId(string $transactionId): Order
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getOrderDate(): DateTime
    {
        return $this->orderDate;
    }

    /**
     * @param DateTime $orderDate
     * @return Order
     */
    public function setOrderDate(DateTime $orderDate): Order
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPaymentReceivedDate(): ?DateTime
    {
        return $this->paymentReceivedDate;
    }

    /**
     * @param DateTime $paymentReceivedDate
     * @return Order
     */
    public function setPaymentReceivedDate(DateTime $paymentReceivedDate): Order
    {
        $this->paymentReceivedDate = $paymentReceivedDate;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Order
     */
    public function setCustomer(Customer $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\Order::class);;
    }

    public function getNotificationDate()
    {
        return $this->getOrderDate();
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }
}