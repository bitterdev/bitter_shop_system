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

use Bitter\BitterShopSystem\Entity\Product as ProductEntity;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Export\Item\ItemInterface;
use Bitter\BitterShopSystem\Entity\Order as OrderEntity;
use SimpleXMLElement;
use DateTime;

class Order implements ItemInterface
{
    /**
     * @param $mixed OrderEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('order');

        $element->addAttribute('transaction-id', $mixed->getTransactionId());
        $element->addAttribute('id', $mixed->getId());
        $element->addAttribute('total', $mixed->getTotal());
        $element->addAttribute('tax', $mixed->getTax());
        $element->addAttribute('subtotal', $mixed->getSubtotal());
        $element->addAttribute('payment-provider-handle', $mixed->getPaymentProviderHandle());
        $element->addAttribute('payment-deceived', (int)$mixed->isPaymentReceived());
        $element->addAttribute('payment-deceived-date', $mixed->getPaymentReceivedDate() instanceof DateTime ? $mixed->getPaymentReceivedDate()->format("Y-m-d H:i:s") : null);
        $element->addAttribute('order-date', $mixed->getOrderDate() instanceof DateTime ? $mixed->getOrderDate()->format("Y-m-d H:i:s") : null);
        $element->addAttribute('package', $mixed->getPackageHandle());

        $positions = $element->addChild("positions");

        foreach ($mixed->getOrderPositions() as $orderPosition) {
            $position = $positions->addChild("position");
            $position->addAttribute('description', $orderPosition->getDescription());
            $position->addAttribute('quantity', $orderPosition->getQuantity());
            $position->addAttribute('product-handle', $orderPosition->getProduct() instanceof ProductEntity ? $orderPosition->getProduct()->getHandle() : null);
            $position->addAttribute('tax', $orderPosition->getTax());
            $position->addAttribute('price', $orderPosition->getPrice());
        }

        return $element;
    }
}
