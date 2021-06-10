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

use Concrete\Core\Application\Application;
use Concrete\Core\Notification\Type\Manager as CoreManager;

class Manager extends CoreManager
{
    public function createOrderCreatedDriver()
    {
        return $this->app->make(OrderCreatedType::class);
    }
}
