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
use HtmlObject\Element;

/** @var Order $order */

$app = Application::getFacadeApplication();
/** @var MoneyTransformer $moneyTransformer */
$moneyTransformer = $app->make(MoneyTransformer::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
$includeTax = $config->get("bitter_shop_system.display_prices_including_tax", false);

$table = new Element("table", null, ["border" => 1]);

$thead = new Element("tbody", [
    new Element("tr", [
        new Element("th", t("Product")),
        new Element("th", t("Quantity")),
        new Element("th", t("Price"), ["style" => "text-align: right"])
    ])
]);

$table->appendChild($thead);

$tbody = new Element("tbody");

foreach ($order->getOrderPositions() as $orderPosition) {
    $tbody->appendChild(new Element("tr", [
        new Element("td", $orderPosition->getDescription()),
        new Element("td", $orderPosition->getQuantity()),
        new Element("td", $moneyTransformer->transform($orderPosition->getPrice($includeTax)), ["style" => "text-align: right"])
    ]));
}

$tbody->appendChild(new Element("tr", [
    new Element("td", t("Subtotal"), ["colspan" => 2, "style" => "text-align: right"]),
    new Element("td", $moneyTransformer->transform($includeTax ? $order->getTotal() : $order->getSubtotal()), ["style" => "text-align: right"]),
]));

if ($order->getTax() > 0) {
    $tbody->appendChild(new Element("tr", [
        new Element("td", $includeTax ? t("Include Tax") : t("Exclude Tax"), ["colspan" => 2, "style" => "text-align: right"]),
        new Element("td", $moneyTransformer->transform($order->getTax()), ["style" => "text-align: right"]),
    ]));
}

$tbody->appendChild(new Element("tr", [
    new Element("td", t("Total"), ["colspan" => 2, "style" => "text-align: right"]),
    new Element("td", $moneyTransformer->transform($order->getTotal()), ["style" => "text-align: right"]),
]));

$table->appendChild($tbody);

$subject = t("Order Confirmation %s", $order->getId());

$bodyHTML = "<p>" . t("Dear Customer,") . "</p>";
$bodyHTML = "<p>" . t("Thank you for placing your order!") . "</p>";
$bodyHTML = "<p>" . t("Here is the order confirmation for your records.") . "</p>";
$bodyHTML = (string)$table->render();

$body = t("You need to enable HTML in your settings to see the content of this email.");