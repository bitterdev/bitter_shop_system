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

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\Categories\Bulk;

use Bitter\BitterShopSystem\Entity\Category;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/categories/bulk/delete';
    protected $categories = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateCategories();

        $this->set('categories', $this->categories);
        $this->set('excluded', $this->excluded);
    }

    private function populateCategories()
    {
        $categoryIds = $this->request("item");

        if (is_array($categoryIds) && count($categoryIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($categoryIds as $categoryId) {
                $this->categories[] = $entityManager->getRepository(Category::class)->findOneBy(["id" => (int)$categoryId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateCategories();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->categories) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->categories as $category) {
                $entityManager->remove($category);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s category deleted', '%s categories deleted', $count));
        $r->setTitle(t('Categories Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/bitter_shop_system/products/categories'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
