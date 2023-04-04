<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block\BlockType;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Concrete\Core\Error\ErrorList\ErrorList;

interface BlockTypeConfigurationInterface
{
    public function setBlock(Block $block);

    public function getBlock(): Block;

    public function save(): ErrorList;

    public function render();
}