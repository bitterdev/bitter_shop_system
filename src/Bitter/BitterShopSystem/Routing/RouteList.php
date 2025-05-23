<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Routing;

use Bitter\BitterShopSystem\API\V1\Middleware\FractalNegotiatorMiddleware;
use Bitter\BitterShopSystem\API\V1\Payments;
use Bitter\BitterShopSystem\API\V1\PdfEditor;
use Bitter\BitterShopSystem\API\V1\Products;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        /*
         * API (Public)
         */

        $router
            ->buildGroup()
            ->setPrefix('/api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function ($groupRouter) {
                /** @var $groupRouter Router */
                /** @noinspection PhpParamsInspection */
                $groupRouter->all('/payments/process_payment/{paymentProviderHandle}', [Payments::class, 'processPayment']);
                /** @noinspection PhpParamsInspection */
                $groupRouter->all('/payments/products/get_products', [Products::class, 'getProducts']);
            });

        /*
         * API (Private)
         */

        $router
            ->buildGroup()
            ->setPrefix('/api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function ($groupRouter) {
                /** @var $groupRouter Router */
                /** @noinspection PhpParamsInspection */
                $groupRouter->all('/pdf_editor/remove_block', [PdfEditor::class, 'removeBlock']);
                $groupRouter->all('/pdf_editor/resize_block', [PdfEditor::class, 'resizeBlock']);
            });

        /*
         * Products
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Products')
            ->setPrefix('/ccm/system/dialogs/products')
            ->routes('dialogs/products.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/products')
            ->routes('search/products.php', 'bitter_shop_system');

        /*
         * Product Categories
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Categories')
            ->setPrefix('/ccm/system/dialogs/categories')
            ->routes('dialogs/categories.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/categories')
            ->routes('search/categories.php', 'bitter_shop_system');


        /*
         * Customers
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Customers')
            ->setPrefix('/ccm/system/dialogs/customers')
            ->routes('dialogs/customers.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/customers')
            ->routes('search/customers.php', 'bitter_shop_system');

        /*
         * Orders
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Orders')
            ->setPrefix('/ccm/system/dialogs/orders')
            ->routes('dialogs/orders.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/orders')
            ->routes('search/orders.php', 'bitter_shop_system');

        /*
         * Tax Rates
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\TaxRates')
            ->setPrefix('/ccm/system/dialogs/tax_rates')
            ->routes('dialogs/tax_rates.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/tax_rates')
            ->routes('search/tax_rates.php', 'bitter_shop_system');

        /*
         * Shipping Costs
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\ShippingCosts')
            ->setPrefix('/ccm/system/dialogs/shipping_costs')
            ->routes('dialogs/shipping_costs.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/shipping_costs')
            ->routes('search/shipping_costs.php', 'bitter_shop_system');

        /*
         * Coupons
         */

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Coupons')
            ->setPrefix('/ccm/system/dialogs/coupons')
            ->routes('dialogs/coupons.php', 'bitter_shop_system');

        $router->buildGroup()->setNamespace('Concrete\Package\BitterShopSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/coupons')
            ->routes('search/coupons.php', 'bitter_shop_system');

        /*
         * Support API
         */

        $router
            ->buildGroup()
            ->routes('api.php', 'bitter_shop_system');

        /*
         * Support Dialog
         */

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/bitter_shop_system')
            ->routes('dialogs/support.php', 'bitter_shop_system');

        /*
         * Pdf Editor Dialogs
         */

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\BitterShopSystem\Controller\Dialog\PdfEditor')
            ->setPrefix('/ccm/system/dialogs/pdf_editor')
            ->routes('dialogs/pdf_editor.php', 'bitter_shop_system');

        /*
         * Panels
         */

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\BitterShopSystem\Controller\Panel\PdfEditor')
            ->setPrefix('/ccm/system/panels/pdf_editor')
            ->routes('panels.php', 'bitter_shop_system');

        /*
         * Asset Translations
         */

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\BitterShopSystem\Controller\Frontend')
            ->setPrefix('/ccm/assets/localization/bitter_shop_system')
            ->routes('assets_localization.php', 'bitter_shop_system');
    }
}