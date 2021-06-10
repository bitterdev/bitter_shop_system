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
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var Order[] $orders */

$app = Application::getFacadeApplication();
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Date $dateService */
$dateService = $app->make(Date::class);
?>

<?php if (count($orders) === 0) { ?>
    <div class="alert alert-warning">
        <?php echo t("There are no orders available."); ?>
    </div>
<?php } else { ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>
                <?php echo t("Order Number"); ?>
            </th>

            <th>
                <?php echo t("Order Date"); ?>
            </th>

            <th>
                <?php echo t("Total"); ?>
            </th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($orders as $order) { ?>
            <tr>
                <td>
                    <a href="<?php echo Url::to(Page::getCurrentPage(), "details", $order->getId()) ?>">
                        <?php echo t("Order %s", $order->getId()); ?>
                    </a>
                </td>

                <td>
                    <?php echo $dateService->formatDateTime($order->getOrderDate()); ?>
                </td>

                <td>
                    <?php echo $moneyTransformer->transform($order->getTotal()); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
