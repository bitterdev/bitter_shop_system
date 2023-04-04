<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Export\Item\PdfEditor;

use Concrete\Core\Export\Item\ItemInterface;
use SimpleXMLElement;

class Block implements ItemInterface
{
    /**
     * @param $mixed \Bitter\BitterShopSystem\Entity\PdfEditor\Block
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('block');

        $element->addAttribute('block-type-handle', $mixed->getBlockTypeHandle());
        $element->addAttribute('font-color', $mixed->getFontColor());
        $element->addAttribute('font-name', $mixed->getFontName());
        $element->addAttribute('font-size', $mixed->getFontSize());
        $element->addAttribute('height', $mixed->getHeight());
        $element->addAttribute('width', $mixed->getWidth());
        $element->addAttribute('top', $mixed->getTop());
        $element->addAttribute('left', $mixed->getLeft());
        $element->addAttribute('package', $mixed->getPackageHandle());

        $cnode = $element->addChild('content');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDataSection($mixed->getContent()));

        return $element;
    }
}
