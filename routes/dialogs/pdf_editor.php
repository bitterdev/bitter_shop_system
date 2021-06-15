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
 * Base path: /ccm/system/dialogs/pdf_editor
 * Namespace: Concrete\Package\BitterShopSystem\Controller\Dialog\PdfEditor
 */

$router->all('/block_settings/add/{blockTypeHandle}', 'BlockSettings::add');
$router->all('/block_settings/edit/{blockId}', 'BlockSettings::edit');
$router->all('/block_settings/submit/{blockTypeHandle}/{blockId}', 'BlockSettings::submit');
