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
use Bitter\BitterShopSystem\Entity\TaxRate as TaxRateEntity;
use SimpleXMLElement;

class TaxRate implements ItemInterface
{
    /**
     * @param $mixed TaxRateEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('taxrate');

        $element->addAttribute('name', $mixed->getName());
        $element->addAttribute('handle', $mixed->getHandle());
        $element->addAttribute('rate', $mixed->getRate());
        $element->addAttribute('package', $mixed->getPackageHandle());

        $variants = $element->addChild("variants");

        foreach ($mixed->getVariants() as $variantEntry) {
            $variant = $variants->addChild("variant");
            $variant->addAttribute('country', $variantEntry->getCountry());
            $variant->addAttribute('state', $variantEntry->getState());
            $variant->addAttribute('tax-rate', $variantEntry->getTaxRate());
        }

        return $element;
    }
}
