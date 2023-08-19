<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProvider;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var $entry Order */
/** @var $form Form */
$app = Application::getFacadeApplication();
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Date $dateService */
$dateService = $app->make(Date::class);
/** @var CategoryService $service */
$service = $app->make(CategoryService::class);
$categoryEntity = $service->getByHandle('customer');
/** @var CustomerCategory $category */
$category = $categoryEntity->getController();
$setManager = $category->getSetManager();




?>
    <h2>
        <?php echo t("Order Details"); ?>
    </h2>

    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
            <td>
                <?php echo t("Order Number"); ?>
            </td>

            <td>
                <?php echo t("Order %s", $entry->getId()); ?>
            </td>
        </tr>


        <tr>
            <td>
                <?php echo t("Order Date"); ?>
            </td>

            <td>
                <?php echo $dateService->formatDateTime($entry->getOrderDate()); ?>
            </td>
        </tr>

        <?php if ($entry->getPaymentProvider() instanceof PaymentProvider) { ?>
            <tr>
                <td>
                    <?php echo t("Payment Provider"); ?>
                </td>

                <td>
                    <?php echo $entry->getPaymentProvider()->getName(); ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td>
                <?php echo t("Payment Received"); ?>
            </td>

            <td>
                <?php echo $entry->isPaymentReceived() ? t("Yes") : t("No"); ?>
            </td>
        </tr>

        <?php if ($entry->getPaymentReceivedDate() instanceof DateTime) { ?>
            <tr>
                <td>
                    <?php echo t("Payment Received Date"); ?>
                </td>

                <td>
                    <?php echo $dateService->formatDateTime($entry->getPaymentReceivedDate()); ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td>
                <?php echo t("Transaction Id"); ?>
            </td>

            <td>
                <?php echo $entry->getTransactionId() ?? t("None"); ?>
            </td>
        </tr>

        </tbody>
    </table>
<?php if ($entry->getCustomer() instanceof Customer) { ?>

    <h2>
        <?php echo t("Customer Details"); ?>
    </h2>

    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
            <td>
                <?php echo t("Mail Address"); ?>
            </td>

            <td>
                <?php echo $entry->getCustomer()->getEmail(); ?>
            </td>
        </tr>
        <?php foreach ($setManager->getUnassignedAttributeKeys() as $attributeKey) { ?>
            <?php /** @var CustomerKey $attributeKey */ ?>
            <tr>
                <td>
                    <?php echo $attributeKey->getAttributeKeyName(); ?>
                </td>

                <td>
                    <?php echo $entry->getCustomer()->getAttributeValue($attributeKey); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>

    <h2>
        <?php echo t("Order Positions"); ?>
    </h2>

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
        <?php foreach ($entry->getOrderPositions() as $orderPosition) { ?>
            <tr<?php echo $orderPosition->getPrice() < 0 ? " class=\"text-danger\"" : ""; ?>>
                <td>
                    <?php echo $orderPosition->getDescription(); ?>
                </td>

                <td class="text-right">
                    <?php echo $orderPosition->getQuantity(); ?>
                </td>

                <td class="text-right">
                    <?php echo $moneyTransformer->transform($orderPosition->getPrice()); ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <th colspan="2">
                <div class="float-end">
                    <?php echo t("Subtotal"); ?>
                </div>
            </th>

            <th class="text-right">
                <?php echo $moneyTransformer->transform($entry->getSubtotal()); ?>
            </th>
        </tr>

        <?php if ($entry->getTax() > 0) { ?>
            <tr>
                <th colspan="2">
                    <div class="float-end">
                        <?php echo t("Taxes"); ?>
                    </div>
                </th>

                <th class="text-right">
                    <?php echo $moneyTransformer->transform($entry->getTax()); ?>
                </th>
            </tr>
        <?php } ?>

        <tr>
            <th colspan="2">
                <div class="float-end">
                    <?php echo t("Total"); ?>
                </div>
            </th>

            <th class="text-right">
                <?php echo $moneyTransformer->transform($entry->getTotal()); ?>
            </th>
        </tr>
        </tbody>
    </table>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/bitter_shop_system/orders"); ?>" class="btn btn-secondary">
                <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
            </a>
        </div>
    </div>

<?php


