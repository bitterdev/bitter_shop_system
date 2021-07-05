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
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;

class OrderTable extends BlockType implements BlockTypeInterface
{
    public function getHandle(): string
    {
        return "order_table";
    }

    public function getImagePath(): string
    {
        return $this->pkg->getRelativePath() . "/images/pdf_editor/block_types/order_table.png";
    }

    public function getName(): string
    {
        return t("Order Table");
    }

    public function getConfigurationElement(): BlockTypeConfigurationInterface
    {
        return $this->app->make(\Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PdfEditor\BlockTypes\OrderTable::class);
    }

    public function render(Document $document, Block $block, Order $order): Document
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        /** @var Repository $config */
        $config = $app->make(Repository::class);
        $includeTax = $config->get("bitter_shop_system.display_prices_including_tax", false);

        $document->SetXY($block->getLeft(), $block->getTop());
        $document->SetFont($block->getFontName());
        $document->SetFontSize($block->getFontSize());

        $colors = $this->hexToRgb($block->getFontColor());

        $document->SetTextColor($colors["r"], $colors["g"], $colors["b"]);

        $document->Cell($block->getWidth() / 3, 9, utf8_decode(t("Description")), 1, 0, 'L');
        $document->Cell($block->getWidth() / 3, 9, utf8_decode(t("Quantity")), 1, 0, 'L');
        $document->Cell($block->getWidth() / 3, 9, utf8_decode(t("Price")), 1, 0, 'R');

        $document->Ln();

        foreach ($order->getOrderPositions() as $orderPosition) {
            $document->SetX($block->getLeft());
            $document->Cell($block->getWidth() / 3, 9, utf8_decode($orderPosition->getDescription()), 1, 0, 'L');
            $document->Cell($block->getWidth() / 3, 9, utf8_decode($orderPosition->getQuantity()), 1, 0, 'L');
            $document->Cell($block->getWidth() / 3, 9, iconv('UTF-8', 'windows-1252', $moneyTransformer->transform($orderPosition->getPrice($includeTax))), 1, 0, 'R');
            $document->Ln();
        }

        $document->SetX($block->getLeft());
        $document->Cell($block->getWidth() / 3 * 2, 9, utf8_decode(t("Subtotal")), 1, 0, 'R');
        $document->Cell($block->getWidth() / 3, 9, iconv('UTF-8', 'windows-1252', $moneyTransformer->transform($includeTax ? $order->getTotal() : $order->getSubtotal())), 1, 0, 'R');
        $document->Ln();

        $document->SetX($block->getLeft());
        $document->Cell($block->getWidth() / 3 * 2, 9, utf8_decode($includeTax ? t("Include Tax") : t("Exclude Tax")), 1, 0, 'R');
        $document->Cell($block->getWidth() / 3, 9, iconv('UTF-8', 'windows-1252', $moneyTransformer->transform($order->getTax())), 1, 0, 'R');
        $document->Ln();

        $document->SetX($block->getLeft());
        $document->Cell($block->getWidth() / 3 * 2, 9, utf8_decode(t("Total")), 1, 0, 'R');
        $document->Cell($block->getWidth() / 3, 9, iconv('UTF-8', 'windows-1252', $moneyTransformer->transform($order->getTotal())), 1, 0, 'R');

        return $document;
    }
}
