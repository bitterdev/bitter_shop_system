<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Concrete\Package\BitterShopSystem\Controller\Element\Dashboard\PdfEditor\BlockTypes;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockType\BlockTypeConfigurationInterface;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\ErrorList\ErrorList;

class OrderTable extends ElementController implements BlockTypeConfigurationInterface
{
    protected $pkgHandle = "bitter_shop_system";

    /** @var Block */
    protected $block;

    public function getElement()
    {
        return "dashboard/pdf_editor/block_types/order_table";
    }

    public function setBlock(Block $block)
    {
        $this->block = $block;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function save(): ErrorList
    {
        return new ErrorList();
    }

    public function view()
    {
        $this->set("block", $this->getBlock());
    }
}