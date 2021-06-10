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

namespace Bitter\BitterShopSystem\Entity\Notification;

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Notification\View\OrderCreatedListView;
use Concrete\Core\Entity\Notification\Notification;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\View\StandardListView;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="OrderCreatedNotifications"
 * )
 */
class OrderCreatedNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Order", cascade={"persist", "remove"}, inversedBy="notifications"),
     * @ORM\JoinColumn(name="orderId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $order;

    /**
     * @param Order $order
     */
    public function __construct(SubjectInterface $order)
    {
        $this->order = $order;
        parent::__construct($order);
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }


    public function getListView()
    {
        return new OrderCreatedListView($this);
    }
}
