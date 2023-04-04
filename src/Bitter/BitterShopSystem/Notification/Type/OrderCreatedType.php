<?php /** @noinspection PhpInconsistentReturnPointsInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Notification\Type;

use Bitter\BitterShopSystem\Entity\Notification\OrderCreatedNotification;
use Bitter\BitterShopSystem\Entity\Order;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Concrete\Core\Notification\Type\Type;

class OrderCreatedType extends Type
{
    /**
     * @param $order Order
     */
    public function createNotification(SubjectInterface $order)
    {
        return new OrderCreatedNotification($order);
    }

    protected function createSubscription()
    {
        return new StandardSubscription('order_created', t('Orders created'));
    }

    public function getSubscription(SubjectInterface $subject)
    {
        return $this->createSubscription();
    }

    public function getAvailableSubscriptions()
    {
        return array($this->createSubscription());
    }

    public function getAvailableFilters()
    {
        return [new StandardFilter($this, 'order_created', t('Orders created'), 'ordercreatednotification')];
    }

}