<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

/** @var \Bitter\BitterShopSystem\Entity\Order $order */

$subject = t("Order Confirmation %s", $order->getId());

$bodyHTML = "<p>" . t("Dear Customer,") . "</p>";
$bodyHTML .= "<p>" . t("Thank you for placing your order!") . "</p>";
$bodyHTML .= "<p>" . t("There is a order confirmation attached for your records.") . "</p>";

$body = t("Dear Customer,") . "\n\n";
$body .= t("Thank you for placing your order!") . "\n\n";
$body .= t("There is a order confirmation attached for your records.");