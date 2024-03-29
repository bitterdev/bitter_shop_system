<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\SinglePage\Dashboard\BitterShopSystem\Customers;

use Bitter\BitterShopSystem\Attribute\Key\CustomerKey;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;
use Concrete\Core\Support\Facade\Url;

class Attributes extends DashboardAttributesPageController
{
    public function view()
    {
        $this->renderList();
    }

    public function edit($akID = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->renderEdit(CustomerKey::getByID($akID), Url::to('/dashboard/bitter_shop_system/customers/attributes', 'view'));
    }

    public function update($akID = null)
    {
        $this->edit($akID);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->executeUpdate(CustomerKey::getByID($akID), Url::to('/dashboard/bitter_shop_system/customers/attributes', 'view'));
    }

    public function select_type($type = null)
    {
        /** @var TypeFactory $typeFactory */
        $typeFactory = $this->app->make(TypeFactory::class);
        $this->renderAdd($typeFactory->getByID($type), Url::to('/dashboard/bitter_shop_system/customers/attributes', 'view'));
    }

    public function add($type = null)
    {
        $this->select_type($type);
        /** @var TypeFactory $typeFactory */
        $typeFactory = $this->app->make(TypeFactory::class);
        $this->executeAdd($typeFactory->getByID($type), Url::to('/dashboard/bitter_shop_system/customers/attributes', 'view'));
    }


    public function delete($akID = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->executeDelete(CustomerKey::getByID($akID), Url::to('/dashboard/bitter_shop_system/customers/attributes', 'view'));
    }

    protected function getCategoryObject()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Category::getByHandle('customer');
    }
}