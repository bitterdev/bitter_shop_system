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

namespace Bitter\BitterShopSystem\Events;

use Bitter\BitterShopSystem\Entity\Customer;
use Symfony\Component\EventDispatcher\GenericEvent;

class CustomerCreated extends GenericEvent
{
    /** @var Customer */
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
     * @return CustomerCreated
     */
    public function setCustomer(Customer $customer): CustomerCreated
    {
        $this->customer = $customer;
        return $this;
    }
}