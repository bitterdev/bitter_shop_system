<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block\BlockType;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{
    /** @var Application */
    protected $app;

    public function __construct(Application $application)
    {
        /** @noinspection PhpParamsInspection */
        parent::__construct($application);

        $this->driver('content');
        $this->driver('order_table');
    }

    public function createContentDriver()
    {
        return $this->app->make(Content::class);
    }

    public function createOrderTableDriver()
    {
        return $this->app->make(OrderTable::class);
    }
}