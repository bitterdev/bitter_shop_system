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

/**
 * @var Router $router
 * Base path: /ccm/system/panels/pdf_editor
 * Namespace: Concrete\Package\BitterShopSystem\Controller\Panel\PdfEditor
 */

$router->all('/blocks/add', 'Blocks\Add::view');
$router->all('/document', 'Document::view');
$router->all('/document/general', 'Document\General::view');
$router->all('/document/general/submit', 'Document\General::submit');
$router->all('/document/letterhead', 'Document\Letterhead::view');
$router->all('/document/letterhead/submit', 'Document\Letterhead::submit');
$router->all('/document/margins', 'Document\Margins::view');
$router->all('/document/margins/submit', 'Document\Margins::submit');
$router->all('/document/paper_size', 'Document\PaperSize::view');
$router->all('/document/paper_size/submit', 'Document\PaperSize::submit');
