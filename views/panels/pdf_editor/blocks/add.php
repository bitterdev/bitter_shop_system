<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

use Bitter\BitterShopSystem\PdfEditor\Block\BlockType\BlockTypeInterface;

defined('C5_EXECUTE') or die('Access denied');

/** @var BlockTypeInterface[] $blockTypes */

?>

<section>
    <div data-panel-menu="accordion" class="ccm-panel-header-accordion">
        <nav>
            <?php echo t("Block Types"); ?>
        </nav>
    </div>

    <div class="ccm-panel-content-inner" id="ccm-panel-add-blocktypes-list">
        <div class="ccm-panel-add-block-set">
            <ul>
                <?php foreach ($blockTypes as $blockType) { ?>
                    <li>
                        <a data-block-type="<?php echo h($blockType->getHandle()); ?>"
                           class="ccm-panel-add-block-draggable-block-type" href="javascript:void(0)">
                            <p>
                                <img src="<?php echo $blockType->getImagePath(); ?>"
                                     alt="<?php echo h($blockType->getName()); ?>">

                                <span><?php echo $blockType->getName(); ?></span>
                            </p>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</section>

<style type="text/css">
    .ccm-panel-add-block-draggable-block-type {
        cursor: pointer !important;
    }
</style>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $(".ccm-panel-add-block-draggable-block-type").click(function () {
                window.ConcretePanelManager.getByIdentifier("add-block").hide()

                $.fn.dialog.open({
                    width: 550,
                    height: "80%",
                    title: ccmi18n_bitter_shop_system.editSettingsDialogTitle,
                    href: CCM_DISPATCHER_FILENAME + "/ccm/system/dialogs/pdf_editor/block_settings/add/" + $(this).data("blockType")
                });
            });
        });
    })(jQuery);
</script>