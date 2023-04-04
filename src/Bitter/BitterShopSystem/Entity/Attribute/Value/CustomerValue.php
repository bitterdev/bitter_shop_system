<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Entity\Attribute\Value;

use Bitter\BitterShopSystem\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="CustomerAttributeValues"
 * )
 */
class CustomerValue extends AbstractValue
{
    /**
     * @var Customer
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Customer", inversedBy="attributes")
     * @ORM\JoinColumn(name="customerId", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return CustomerValue
     */
    public function setCustomer(Customer $customer): CustomerValue
    {
        $this->customer = $customer;
        return $this;
    }
}
