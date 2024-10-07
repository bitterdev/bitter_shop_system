<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block\BlockType;

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Bitter\BitterShopSystem\PdfEditor\Document;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Content extends BlockType implements BlockTypeInterface
{
    public function getHandle(): string
    {
        return "content";
    }

    public function getImagePath(): string
    {
        return $this->pkg->getRelativePath() . "/images/pdf_editor/block_types/content.png";
    }

    public function getName(): string
    {
        return t("Content");
    }

    public function getConfigurationElement(): BlockTypeConfigurationInterface
    {
        return $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PdfEditor\BlockTypes\Content::class);
    }

    public function render(Document $document, Block $block, Order $order): Document
    {
        $document->SetXY($block->getLeft(), $block->getTop());
        $document->SetFont($block->getFontName());
        $document->SetFontSize($block->getFontSize());

        $colors = $this->hexToRgb($block->getFontColor());

        $document->SetTextColor($colors["r"], $colors["g"], $colors["b"]);
        $document->MultiCell($block->getWidth(), 6, utf8_decode(preg_replace_callback("/\{{([a-z0-9_.]+?)\}}/i", function ($result) use ($order) {
            if (isset($result[1])) {
                $accessor = PropertyAccess::createPropertyAccessor();
                return str_replace("\n\n", "\n", $accessor->getValue($order, $result[1]));
            }
        }, $block->getContent())));

        return $document;
    }
}
