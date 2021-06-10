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

namespace Bitter\BitterShopSystem\Notification\View;

use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Notification\OrderCreatedNotification;
use Bitter\BitterShopSystem\Entity\Order;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Notification\View\StandardListView;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Url;
use HtmlObject\Element;

class OrderCreatedListView extends StandardListView
{
    /**
     * @var OrderCreatedNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Order Created');
    }

    public function getIconClass()
    {
        return 'fa fa-shopping-cart';
    }

    public function getInitiatorUserObject()
    {
        $order = $this->notification->getOrder();

        if ($order instanceof Order) {
            $customer = $order->getCustomer();

            if ($customer instanceof Customer) {
                $userEntity = $customer->getUser();

                if ($userEntity instanceof User) {
                    return $userEntity->getUserInfoObject();
                }
            }
        }
    }

    public function getActionDescription()
    {
        $order = $this->notification->getOrder();

        /** @noinspection HtmlUnknownTarget */
        return t(
            "New order <a href=\"%s\">%s</a> created.",
            (string)Url::to("/dashboard/bitter_shop_system/orders/details", $order->getId()),
            $order->getId()
        );
    }

}