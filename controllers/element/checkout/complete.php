<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Checkout;

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Controller\ElementController;

class Complete extends ElementController
{
    protected $pkgHandle = "bitter_shop_system";

    public function getElement(): string
    {
        return '/checkout/complete';
    }

    public function view()
    {
        /** @var CheckoutService $checkoutService */
        $checkoutService = $this->app->make(CheckoutService::class);
        $checkoutService->completeOrder();
    }
}
