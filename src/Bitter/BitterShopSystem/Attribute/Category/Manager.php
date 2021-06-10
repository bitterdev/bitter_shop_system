<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Attribute\Category;

use Concrete\Core\Attribute\Category\Manager as CoreManager;

class Manager extends CoreManager
{
    public function createCustomerDriver(): CustomerCategory
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(CustomerCategory::class);
    }

    public function createProductDriver(): ProductCategory
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->app->make(ProductCategory::class);
    }
}
