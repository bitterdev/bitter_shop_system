<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/** @var Router $router */

$router
    ->buildGroup()
    ->setPrefix("/bitter_shop_system/api")
    ->setNamespace("Bitter\BitterShopSystem\Controller")
    ->routes(function($groupRouter) {
        $groupRouter->get('/hide_reminder', 'Api::hideReminder');
        $groupRouter->get('/hide_did_you_know', 'Api::hideDidYouKnow');
        $groupRouter->get('/hide_license_check', 'Api::hideLicenseCheck');
    });