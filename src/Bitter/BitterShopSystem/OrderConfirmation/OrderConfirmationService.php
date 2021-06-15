<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\OrderConfirmation;

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Bitter\BitterShopSystem\PdfEditor\Document;
use Concrete\Core\Config\Repository\Repository;

class OrderConfirmationService
{
    protected $blockService;
    protected $config;

    public function __construct(
        BlockService $blockService,
        Repository $config
    )
    {
        $this->blockService = $blockService;
        $this->config = $config;
    }

    public function createPdfOrderConfirmation(Order $order): Document
    {
        $document = new Document(
            $this->config->get("bitter_shop_system.pdf_editor.paper_size.page_orientation", "portrait") === "portrait" ? "P" : "L",
            "mm",
            [
                (int)$this->config->get("bitter_shop_system.pdf_editor.paper_size.width", 210),
                (int)$this->config->get("bitter_shop_system.pdf_editor.paper_size.height", 297)
            ]
        );

        $document->SetMargins(
            (int)$this->config->get("bitter_shop_system.pdf_editor.margins.left", 25),
            (int)$this->config->get("bitter_shop_system.pdf_editor.margins.top", 45),
            (int)$this->config->get("bitter_shop_system.pdf_editor.margins.right", 20)
        );

        $document->SetAutoPageBreak(true, (int)$this->config->get("bitter_shop_system.pdf_editor.margins.bottom", 45));

        $document->AddPage();

        foreach ($this->blockService->getAll() as $block) {
            $block->getBlockType()->render($document, $block, $order);
        }

        return $document;
    }
}