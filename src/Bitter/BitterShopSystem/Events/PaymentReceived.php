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

use Bitter\BitterShopSystem\Entity\Order;
use Symfony\Component\EventDispatcher\GenericEvent;

class PaymentReceived extends GenericEvent
{
    /** @var Order */
    protected $order;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return PaymentReceived
     */
    public function setOrder(Order $order): PaymentReceived
    {
        $this->order = $order;
        return $this;
    }
}