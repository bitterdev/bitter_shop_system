<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\MigrationTool\Exporter\Item\Type;

use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\Manager as CoreManager;

class Manager extends CoreManager
{
    protected function createCustomerDriver()
    {
        return new Customer();
    }

    protected function createProductDriver()
    {
        return new Product();
    }

    protected function createCategoryDriver()
    {
        return new Category();
    }

    protected function createOrderDriver()
    {
        return new Order();
    }

    protected function createShippingCostDriver()
    {
        return new ShippingCost();
    }

    protected function createTaxRateDriver()
    {
        return new TaxRate();
    }

    protected function createCouponDriver()
    {
        return new Coupon();
    }

    protected function createPdfEditorBlockDriver()
    {
        return new PdfEditor\Block();
    }

    public function __construct($app)
    {
        parent::__construct($app);

        $this->driver('product');
        $this->driver('order');
        $this->driver('customer');
        $this->driver('shipping_cost');
        $this->driver('tax_rate');
        $this->driver('coupon');
        $this->driver('category');
        $this->driver('pdf_editor_block');
    }
}