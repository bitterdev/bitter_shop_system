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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\Customers\Bulk;

use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/customers/bulk/delete';
    protected $customers = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateCustomers();

        $this->set('customers', $this->customers);
        $this->set('excluded', $this->excluded);
    }

    private function populateCustomers()
    {
        $customerIds = $this->request("item");

        if (is_array($customerIds) && count($customerIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($customerIds as $customerId) {
                $this->customers[] = $entityManager->getRepository(Customer::class)->findOneBy(["id" => (int)$customerId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateCustomers();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->customers) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->customers as $customer) {
                $entityManager->remove($customer);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s customer deleted', '%s customers deleted', $count));
        $r->setTitle(t('Customers Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/customers'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
