<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\PdfEditor\Block\Menu;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var BlockService $blockService */
$blockService = $app->make(BlockService::class);

?>

<div class="ccm-pdf-editor"></div>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $(".ccm-pdf-editor").pdfEditor(<?php echo json_encode([
                "menu" => new Menu(),
                "enableGrid" => (bool)$config->get("bitter_shop_system.pdf_editor.general.enable_grid", true),
                "gridSize" => (int)$config->get("bitter_shop_system.pdf_editor.general.grid_size", 5),
                "blocks" => $blockService->getAll(),
                "paperSize" => [
                    "width" => (int)$config->get("bitter_shop_system.pdf_editor.paper_size.width", 210),
                    "height" => (int)$config->get("bitter_shop_system.pdf_editor.paper_size.height", 297),
                    "pageOrientation" => $config->get("bitter_shop_system.pdf_editor.paper_size.page_orientation", "portrait")
                ]
            ]); ?>);
        });
    })(jQuery);
</script>