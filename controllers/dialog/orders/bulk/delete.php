<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\Orders\Bulk;

use Bitter\BitterShopSystem\Entity\Order;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/orders/bulk/delete';
    protected $orders = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateOrders();

        $this->set('orders', $this->orders);
        $this->set('excluded', $this->excluded);
    }

    private function populateOrders()
    {
        $orderIds = $this->request("item");

        if (is_array($orderIds) && count($orderIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($orderIds as $orderId) {
                $this->orders[] = $entityManager->getRepository(Order::class)->findOneBy(["id" => (int)$orderId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateOrders();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->orders) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->orders as $order) {
                $entityManager->remove($order);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s order deleted', '%s orders deleted', $count));
        $r->setTitle(t('Orders Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/orders'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
