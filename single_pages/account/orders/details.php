<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;

/** @var Order $order */

$app = Application::getFacadeApplication();
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
$includeTax = $config->get("bitter_shop_system.display_prices_including_tax", false);
?>
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
            <?php echo t("Price"); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order->getOrderPositions() as $orderPosition) { ?>
        <tr<?php echo $orderPosition->getPrice() < 0 ? " class=\"text-danger\"" : ""; ?>>
            <td>
                <?php echo $orderPosition->getDescription(); ?>
            </td>

            <td class="text-right">
                <?php echo $orderPosition->getQuantity(); ?>
            </td>

            <td class="text-right">
                <?php echo $moneyTransformer->transform($orderPosition->getPrice($includeTax)); ?>
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="2">
            <div class="pull-right">
                <?php echo t("Subtotal"); ?>
            </div>
        </td>

        <td class="text-right">
            <?php echo $moneyTransformer->transform($includeTax ? $order->getTotal() : $order->getSubtotal()); ?>
        </td>
    </tr>

    <?php if ($order->getTax() > 0) { ?>
        <tr>
            <td colspan="2">
                <div class="pull-right">
                    <?php echo $includeTax ? t("Include Tax") : t("Exclude Tax"); ?>
                </div>
            </td>

            <td class="text-right">
                <?php echo $moneyTransformer->transform($order->getTax()); ?>
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td colspan="2">
            <div class="pull-right">
                <?php echo t("Total"); ?>
            </div>
        </td>

        <td class="text-right">
            <?php echo $moneyTransformer->transform($order->getTotal()); ?>
        </td>
    </tr>
    </tbody>
</table>
