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

use Bitter\BitterShopSystem\Entity\TaxRate as TaxRateEntity;
use Concrete\Core\Export\Item\ItemInterface;
use Bitter\BitterShopSystem\Entity\ShippingCost as ShippingCostEntity;
use SimpleXMLElement;

class ShippingCost implements ItemInterface
{
    /**
     * @param $mixed ShippingCostEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('shippingcost');

        $element->addAttribute('name', $mixed->getName());
        $element->addAttribute('handle', $mixed->getHandle());
        $element->addAttribute('price', $mixed->getPrice());
        $element->addAttribute('tax-rate', $mixed->getTaxRate() instanceof TaxRateEntity ? $mixed->getTaxRate()->getHandle() : null);
        $element->addAttribute('package', $mixed->getPackageHandle());

        $variants = $element->addChild("variants");

        foreach ($mixed->getVariants() as $variantEntry) {
            $variant = $variants->addChild("variant");
            $variant->addAttribute('country', $variantEntry->getCountry());
            $variant->addAttribute('state', $variantEntry->getState());
            $variant->addAttribute('price', $variantEntry->getPrice());
        }

        return $element;
    }
}
