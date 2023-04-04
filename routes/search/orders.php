<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 * Base path: /ccm/system/search/orders
 * Namespace: Concrete\Package\BitterShopSystem\Controller\Search\
 */

$router->all('/basic', 'Orders::searchBasic');
$router->all('/current', 'Orders::searchCurrent');
$router->all('/preset/{presetID}', 'Order:s:searchPreset');
$router->all('/clear', 'Orders::clearSearch');
