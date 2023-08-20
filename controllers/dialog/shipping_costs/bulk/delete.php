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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\ShippingCosts\Bulk;

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/shipping_costs/bulk/delete';
    protected $shippingCosts = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateShippingCosts();

        $this->set('shippingCosts', $this->shippingCosts);
        $this->set('excluded', $this->excluded);
    }

    private function populateShippingCosts()
    {
        $shippingCostIds = $this->request("item");

        if (is_array($shippingCostIds) && count($shippingCostIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($shippingCostIds as $shippingCostId) {
                $this->shippingCosts[] = $entityManager->getRepository(ShippingCost::class)->findOneBy(["id" => (int)$shippingCostId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateShippingCosts();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->shippingCosts) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->shippingCosts as $shippingCost) {
                $entityManager->remove($shippingCost);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s shipping cost deleted', '%s shipping costs deleted', $count));
        $r->setTitle(t('Shipping Costs Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/shipping_costs'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
