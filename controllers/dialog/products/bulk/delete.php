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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\Products\Bulk;

use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/products/bulk/delete';
    protected $products = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateProducts();

        $this->set('products', $this->products);
        $this->set('excluded', $this->excluded);
    }

    private function populateProducts()
    {
        $productIds = $this->request("item");

        if (is_array($productIds) && count($productIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($productIds as $productId) {
                $this->products[] = $entityManager->getRepository(Product::class)->findOneBy(["id" => (int)$productId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateProducts();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->products) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->products as $product) {
                $entityManager->remove($product);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s product deleted', '%s products deleted', $count));
        $r->setTitle(t('Products Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/products'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
