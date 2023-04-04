<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\View\View;

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "bitter_shop_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/bitter-shop-system/reviews"], 'bitter_shop_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');

/** @var KeyList $attributeView */
$attributeView->render();

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "bitter_shop_system"], 'bitter_shop_system');