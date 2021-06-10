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

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue;
use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Customer implements ObjectInterface, ExportableInterface
{
    use PackageTrait;
    use ObjectTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @var Collection|Order[]
     * @ORM\ManyToMany(targetEntity="\Bitter\BitterShopSystem\Entity\Order", cascade={"persist"})
     * @ORM\JoinTable(name="CustomerOrders",
     * joinColumns={@ORM\JoinColumn(name="customerId", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="orderId", referencedColumnName="id")}
     * )
     */
    protected $orders;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var \Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue[]
     * @ORM\OneToMany(targetEntity="\Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue", mappedBy="customer", orphanRemoval=true)
     */
    protected $attributes;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Customer
     */
    public function setId(int $id): Customer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Order[]|Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order[]|Collection $orders
     * @return Customer
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Customer
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Customer
     */
    public function setEmail(string $email): Customer
    {
        $this->email = $email;
        return $this;
    }

    public function getObjectAttributeCategory(): CustomerCategory
    {
        $app = Application::getFacadeApplication();
        return $app->make(CustomerCategory::class);
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!($ak instanceof AttributeKeyInterface)) {
            $ak = $ak ? $this->getObjectAttributeCategory()->getAttributeKeyByHandle((string)$ak) : null;
        }

        if ($ak === null) {
            $result = null;
        } else {
            $result = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
            if ($result === null && $createIfNotExists) {
                $result = new CustomerValue();
                $result->setCustomer($this);
                $result->setAttributeKey($ak);
            }
        }

        return $result;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\Customer::class);;
    }
}