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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\Coupons\Bulk;

use Bitter\BitterShopSystem\Entity\Coupon;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/coupons/bulk/delete';
    protected $coupons = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateCoupons();

        $this->set('coupons', $this->coupons);
        $this->set('excluded', $this->excluded);
    }

    private function populateCoupons()
    {
        $couponIds = $this->request("item");

        if (is_array($couponIds) && count($couponIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($couponIds as $couponId) {
                $this->coupons[] = $entityManager->getRepository(Coupon::class)->findOneBy(["id" => (int)$couponId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateCoupons();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->coupons) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->coupons as $coupon) {
                $entityManager->remove($coupon);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s coupon deleted', '%s coupons deleted', $count));
        $r->setTitle(t('Coupons Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/coupons'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
