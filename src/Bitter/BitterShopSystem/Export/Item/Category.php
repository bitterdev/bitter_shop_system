<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Export\Item;

use Concrete\Core\Export\Item\ItemInterface;
use Bitter\BitterShopSystem\Entity\Category as CategoryEntity;
use SimpleXMLElement;

class Category implements ItemInterface
{
    /**
     * @param $mixed CategoryEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('category');

        $element->addAttribute('name', $mixed->getName());
        $element->addAttribute('handle', $mixed->getHandle());
        $element->addAttribute('package', $mixed->getPackageHandle());

        return $element;
    }
}
