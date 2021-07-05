<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Error\ErrorList\Formatter\BootstrapFormatter;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var int|null $checkoutPageId */
/** @var ErrorList|null $error */
/** @var string|null $success */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var CheckoutService $cartService */
$cartService = $app->make(CheckoutService::class);
$checkoutPage = Page::getByID($checkoutPageId);
$includeTax = $config->get("bitter_shop_system.display_prices_including_tax", false);
?>
<div class="cart">
    <?php if (isset($success)) { ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <?php if ($error instanceof ErrorList && $error->has()) {
        $formatter = new BootstrapFormatter($error);
        echo $formatter->render();
    } ?>

    <?php if (count($cartService->getAllItems()) === 0) { ?>
        <div class="alert alert-warning">
            <?php echo t("There are no items in your cart."); ?>
        </div>
    <?php } else { ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>
                    <?php echo t("Product"); ?>
                </th>

                <th class="text-right">
                    <?php echo t("Quantity"); ?>
                </th>

                <th class="text-right">
                    <?php echo t("Unit Price"); ?>
                </th>

                <th class="text-right">
                    <?php echo t("Total Price"); ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($cartService->getAllItems() as $cartItem) { ?>
                <tr>
                    <td>
                        <?php echo $cartItem->getProduct()->getName(); ?>

                        <div>
                            <a href="<?php echo Url::to(Page::getCurrentPage(), "remove", $cartItem->getProduct()->getHandle()); ?>">
                                <?php echo t("Remove"); ?>
                            </a>
                        </div>
                    </td>

                    <td class="text-right">
                        <form action="<?php echo Url::to(Page::getCurrentPage(), "update", $cartItem->getProduct()->getHandle()); ?>"
                              method="get">

                            <?php
                            $quantityValues = [];
                            $quantityValues = [];
                            $maxQuantity = (int)$config->get("bitter_shop_system.max_quantity", $cartItem->getProduct()->getQuantity());
                            for ($i = 1; $i <= $maxQuantity; $i++) {
                                $quantityValues[$i] = $i;
                            }
                            ?>

                            <?php echo $form->select("quantity", $quantityValues, $cartItem->getQuantity(), ["class" => "quantity-selector"]); ?>
                        </form>
                    </td>

                    <td class="text-right">
                        <?php echo $moneyTransformer->transform($cartItem->getProduct()->getPrice($includeTax)); ?>
                    </td>

                    <td class="text-right">
                        <?php echo $moneyTransformer->transform($cartItem->getSubtotal($includeTax)); ?>
                    </td>
                </tr>
            <?php } ?>

            <?php if ($cartService->getHighestShippingCostEntry() instanceof ShippingCost) { ?>
                <tr>
                    <td>
                        <?php echo t("Shipping"); ?>
                    </td>

                    <td class="text-right">
                        1
                    </td>

                    <td class="text-right">
                        <?php echo $moneyTransformer->transform($cartService->getHighestShippingCost($includeTax)); ?>
                    </td>

                    <td class="text-right">
                        <?php echo $moneyTransformer->transform($cartService->getHighestShippingCost($includeTax)); ?>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <th colspan="3">
                    <div class="pull-right">
                        <?php echo t("Subtotal"); ?>
                    </div>
                </th>

                <th class="text-right">
                    <?php echo $moneyTransformer->transform($includeTax ? $cartService->getTotal(true, false) : $cartService->getSubtotal(true, false)); ?>
                </th>
            </tr>

            <?php if ($cartService->getTax() > 0) { ?>
                <tr>
                    <th colspan="3">
                        <div class="pull-right">
                            <?php echo $includeTax ? t("Including Taxes") : t("Excluding Taxes"); ?>
                        </div>
                    </th>

                    <th class="text-right">
                        <?php echo $moneyTransformer->transform($cartService->getTax(true, false)); ?>
                    </th>
                </tr>
            <?php } ?>

            <tr>
                <th colspan="3">
                    <div class="pull-right">
                        <?php echo t("Total"); ?>
                    </div>
                </th>

                <th class="text-right">
                    <?php echo $moneyTransformer->transform($cartService->getTotal(true, false)); ?>
                </th>
            </tr>
            </tbody>
        </table>

        <p class="text-muted">
            <?php echo t("Shipping and taxes are estimated based on your current location and will be correctly calculated during the checkout process depending on your shipping and billing address.") ?>
        </p>

        <?php if (!$checkoutPage->isError()) { ?>
            <div class="pull-right">
                <a href="<?php echo Url::to($checkoutPage); ?>" class="btn btn-primary">
                    <?php echo t("Checkout"); ?>

                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
            </div>
        <?php } ?>
    <?php } ?>
</div>
