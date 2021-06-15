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

interface BlockTypeInterface
{
    public function getConfigurationElement(): BlockTypeConfigurationInterface;

    public function getHandle(): string;

    public function getImagePath(): string;

    public function getName(): string;

    public function render(Document $document, Block $block, Order $order): Document;
}
