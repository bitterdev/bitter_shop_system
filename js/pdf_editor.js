/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 *
 */

(function ($) {
    'use strict';

    $.fn.pdfEditor = function (options) {
        var defaults = {
            enableGrid: false,
            gridSize: 0,
            paperSize: {
                width: 0,
                height: 0,
                pageOrientation: "portrait"
            }
        };

        options = $.extend(defaults, options);

        // init toolbar
        $("#ccm-toolbar ul")
            .append(
                $("<li/>")
                    .addClass("float-start hidden-xs")
                    .append(
                        $("<a/>")
                            .attr("data-launch-panel", "document")
                            .attr("title", ccmi18n_bitter_shop_system.editTemplateSettings)
                            .addClass("tooltip")
                            .append(
                                $("<i/>")
                                    .addClass("fa fa-cog")
                            )
                            .append(
                                $("<span/>")
                                    .addClass("ccm-toolbar-accessibility-title")
                                    .addClass("ccm-toolbar-accessibility-title-settings")
                                    .html(ccmi18n_bitter_shop_system.templateSettings)
                            )
                    )
            )
            .append(
                $("<li/>")
                    .addClass("float-start hidden-xs")
                    .append(
                        $("<a/>")
                            .attr("data-launch-panel", "add-block")
                            .attr("title", ccmi18n_bitter_shop_system.addContentToTemplate)
                            .addClass("tooltip")
                            .append(
                                $("<i/>")
                                    .addClass("fa fa-plus")
                            )
                            .append(
                                $("<span/>")
                                    .addClass("ccm-toolbar-accessibility-title")
                                    .addClass("ccm-toolbar-accessibility-title-add")
                                    .html(ccmi18n_bitter_shop_system.addContent)
                            )
                    )
            );

        window.ConcretePanelManager.register({
            'overlay': false,
            'identifier': 'document',
            'position': 'left',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/panels/pdf_editor/document'
        });

        window.ConcretePanelManager.register({
            'identifier': 'add-block',
            'position': 'left',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/panels/pdf_editor/blocks/add'
        });

        window.ConcreteToolbar.start();

        return this.each(function () {
            var pdfEditor = {
                $container: null,
                $activeBlock: false,
                activeBlockItemMenu: false,
                enableContextMenu: true,
                init: function ($container) {
                    this.$container = $container;

                    pdfEditor.$container
                        .contextmenu(function () {
                            return false;
                        });

                    $(window)
                        .resize(function () {
                            // resize the container
                            pdfEditor.$container.css({
                                width: pdfEditor.mmToPx(pdfEditor.getDocumentWidth()) * pdfEditor.getRatio(),
                                height: pdfEditor.mmToPx(pdfEditor.getDocumentHeight()) * pdfEditor.getRatio()
                            });

                            // draw the gutter (if enabled)
                            if (options.enableGrid) {
                                var canvasSize = pdfEditor.mmToPx(options.gridSize) * pdfEditor.getRatio();
                                var height = pdfEditor.$container.height();
                                var width = pdfEditor.$container.width();
                                var canvas = $('<canvas/>').attr({
                                    width: width,
                                    height: height
                                }).appendTo(pdfEditor.$container);
                                var context = canvas.get(0).getContext("2d");

                                context.clearRect(0, 0, width, height);

                                for (var x = 0; x <= width; x += canvasSize) {
                                    context.moveTo(x, 0);
                                    context.lineTo(x, height);
                                }

                                for (var y = 0; y <= height; y += canvasSize) {
                                    context.moveTo(0, y);
                                    context.lineTo(width, y);
                                }

                                context.strokeStyle = "#fff";
                                context.stroke();

                                pdfEditor.$container.find("canvas").remove();
                                pdfEditor.$container.css("backgroundImage", "url(" + canvas[0].toDataURL() + ")");
                            } else {
                                pdfEditor.$container.css("backgroundImage", "none");
                            }

                            // resize all blocks
                            pdfEditor.$container.find(".ccm-pdf-block").each(function () {
                                $(this).css("left", pdfEditor.mmToPx($(this).data("left") * pdfEditor.getRatio()));
                                $(this).css("top", pdfEditor.mmToPx($(this).data("top") * pdfEditor.getRatio()));
                                $(this).css("width", pdfEditor.mmToPx($(this).data("width") * pdfEditor.getRatio()));
                                $(this).css("height", pdfEditor.mmToPx($(this).data("height") * pdfEditor.getRatio()));
                            });
                        })
                        .trigger("resize");

                    // init context menu
                    pdfEditor.blockItemMenu.prototype = Object.create(window.ConcreteMenu.prototype);

                    pdfEditor.blockItemMenu.prototype.setupMenuOptions = function ($menu) {
                        ConcreteMenu.prototype.setupMenuOptions($menu);

                        $menu.find('a[data-block-item-action="edit-settings"]').click(function (e) {
                            e.preventDefault();

                            $.fn.dialog.open({
                                width: 550,
                                height: "80%",
                                title: ccmi18n_bitter_shop_system.editSettingsDialogTitle,
                                href: CCM_DISPATCHER_FILENAME + "/ccm/system/dialogs/pdf_editor/block_settings/edit/" + pdfEditor.$activeBlock.data("blockId")
                            });
                        });

                        $menu.find('a[data-block-item-action="remove"]').click(function (e) {
                            e.preventDefault();

                            $.ajax({
                                type: "POST",
                                cache: false,
                                url: CCM_DISPATCHER_FILENAME + "/api/v1/pdf_editor/remove_block",
                                data: {
                                    blockId: pdfEditor.$activeBlock.data("blockId")
                                },
                                success: function () {
                                    pdfEditor.$activeBlock.remove();
                                }
                            });
                        });
                    };

                    // add blocks
                    for (var block of options.blocks) {
                        pdfEditor.addBlock(block.id, block.left, block.top, block.width, block.height);
                    }

                    // subscribe form events
                    ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
                        if (typeof data.response.block !== "undefined") {
                            var block = data.response.block;
                            var $blockElement = pdfEditor.getBlockElement(block.id);

                            if ($blockElement.length) {
                                $blockElement
                                    .data({
                                        blockId: block.id,
                                        left: block.left,
                                        top: block.top,
                                        width: block.width,
                                        height: block.height
                                    })
                                    .css({
                                        top: pdfEditor.mmToPx(block.top * pdfEditor.getRatio()),
                                        left: pdfEditor.mmToPx(block.left * pdfEditor.getRatio()),
                                        width: pdfEditor.mmToPx(block.width * pdfEditor.getRatio()),
                                        height: pdfEditor.mmToPx(block.height * pdfEditor.getRatio())
                                    });
                            } else {
                                pdfEditor.addBlock(block.id, block.left, block.top, block.width, block.height);
                            }
                        }
                    });
                },

                getBlockElement: function (blockId) {
                    return pdfEditor.$container.find(".ccm-pdf-block[data-block-id='" + blockId + "']");
                },

                addBlock: function (blockId, left, top, width, height) {
                    $("<div/>")
                        .addClass("ccm-pdf-block")
                        .attr("data-block-id", blockId)
                        .data({
                            blockId: blockId,
                            left: left,
                            top: top,
                            width: width,
                            height: height
                        })
                        .css({
                            top: pdfEditor.mmToPx(top * pdfEditor.getRatio()),
                            left: pdfEditor.mmToPx(left * pdfEditor.getRatio()),
                            width: pdfEditor.mmToPx(width * pdfEditor.getRatio()),
                            height: pdfEditor.mmToPx(height * pdfEditor.getRatio())
                        })
                        .appendTo(pdfEditor.$container)
                        .click(function (e) {
                            e.preventDefault();

                            if (pdfEditor.enableContextMenu) {
                                pdfEditor.activeBlockItemMenu = new pdfEditor.blockItemMenu($(this), {
                                    menu: options.menu,
                                    handle: 'none',
                                    container: pdfEditor.$container
                                });

                                pdfEditor.$activeBlock = $(this);
                                pdfEditor.activeBlockItemMenu.show(e);
                            }

                            return false;
                        })
                        .draggable({
                            containment: "parent",
                            start: function () {
                                if (pdfEditor.activeBlockItemMenu !== false) {
                                    pdfEditor.activeBlockItemMenu.hide();
                                    pdfEditor.activeBlockItemMenu = false;
                                }

                                pdfEditor.enableContextMenu = false;
                            },
                            stop: function () {
                                $(this).data({
                                    top: pdfEditor.pxToMm($(this).position().top / pdfEditor.getRatio()),
                                    left: pdfEditor.pxToMm($(this).position().left / pdfEditor.getRatio()),
                                    width: pdfEditor.pxToMm($(this).width() / pdfEditor.getRatio()),
                                    height: pdfEditor.pxToMm($(this).height() / pdfEditor.getRatio())
                                });

                                $.ajax({
                                    type: "POST",
                                    cache: false,
                                    url: CCM_DISPATCHER_FILENAME + "/api/v1/pdf_editor/resize_block",
                                    data: {
                                        blockId: $(this).data("blockId"),
                                        top: $(this).data("top"),
                                        left: $(this).data("left"),
                                        width: $(this).data("width"),
                                        height: $(this).data("height")
                                    }
                                });

                                setTimeout(function () {
                                    pdfEditor.enableContextMenu = true;
                                }, 0);
                            }
                        })
                        .resize(function (e) {
                            e.stopPropagation();
                        })
                        .resizable({
                            containment: "parent",
                            start: function () {
                                if (pdfEditor.activeBlockItemMenu !== false) {
                                    pdfEditor.activeBlockItemMenu.hide();
                                    pdfEditor.activeBlockItemMenu = false;
                                }

                                pdfEditor.enableContextMenu = false;
                            },
                            stop: function () {
                                $(this).data({
                                    top: pdfEditor.pxToMm($(this).position().top / pdfEditor.getRatio()),
                                    left: pdfEditor.pxToMm($(this).position().left / pdfEditor.getRatio()),
                                    width: pdfEditor.pxToMm($(this).width() / pdfEditor.getRatio()),
                                    height: pdfEditor.pxToMm($(this).height() / pdfEditor.getRatio())
                                });

                                $.ajax({
                                    type: "POST",
                                    cache: false,
                                    url: CCM_DISPATCHER_FILENAME + "/api/v1/pdf_editor/resize_block",
                                    data: {
                                        blockId: $(this).data("blockId"),
                                        top: $(this).data("top"),
                                        left: $(this).data("left"),
                                        width: $(this).data("width"),
                                        height: $(this).data("height")
                                    }
                                });

                                setTimeout(function () {
                                    pdfEditor.enableContextMenu = true;
                                }, 0);

                            }
                        });
                },

                getDocumentWidth: function () {
                    if (options.paperSize.pageOrientation === "portrait") {
                        return options.paperSize.width;
                    } else {
                        return options.paperSize.height;
                    }
                },

                getDocumentHeight: function () {
                    if (options.paperSize.pageOrientation === "portrait") {
                        return options.paperSize.height;
                    } else {
                        return options.paperSize.width;
                    }
                },

                getAvailableHeight: function () {
                    return $(window).height() - ($("#ccm-dashboard-content header").position().top + $("#ccm-dashboard-content header").height() + 138);
                },

                getRequiredHeight: function () {
                    return this.mmToPx(this.getDocumentHeight());
                },

                getRatio: function () {
                    return this.getAvailableHeight() / this.getRequiredHeight();
                },

                pxToMm: function (px) {
                    var div = document.createElement('div');
                    div.style.display = 'block';
                    div.style.height = '1mm';
                    document.body.appendChild(div);
                    var convert = px / div.offsetHeight;
                    div.parentNode.removeChild(div);
                    return parseInt(convert);
                },

                mmToPx: function (mm) {
                    var div = document.createElement('div');
                    div.style.display = 'block';
                    div.style.height = '1mm';
                    document.body.appendChild(div);
                    var convert = div.offsetHeight * mm;
                    div.parentNode.removeChild(div);
                    return parseInt(convert);
                },

                blockItemMenu: function ($element, options) {
                    if ($element) {
                        ConcreteMenu.call(this, $element, options);
                    }
                }
            };

            pdfEditor.init($(this));
        });
    };
})(jQuery);