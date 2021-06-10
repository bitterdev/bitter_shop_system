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
use Bitter\BitterShopSystem\Entity\Coupon as CouponEntity;
use SimpleXMLElement;
use DateTime;

class Coupon implements ItemInterface
{
    /**
     * @param $mixed CouponEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $element = $element->addChild('coupon');

        $element->addAttribute('code', $mixed->getCode());
        $element->addAttribute('valid-from', $mixed->getValidFrom() instanceof DateTime ? $mixed->getValidFrom()->format("Y-m-d H:i:s") : null);
        $element->addAttribute('valid-to', $mixed->getValidTo() instanceof DateTime ? $mixed->getValidTo()->format("Y-m-d H:i:s") : null);
        $element->addAttribute('use-percentage-discount', $mixed->isUsePercentageDiscount() ? "1" : "0");
        $element->addAttribute('discount-price', $mixed->getDiscountPrice());
        $element->addAttribute('discount-percentage', $mixed->getDiscountPercentage());
        $element->addAttribute('maximum-discount-amount', $mixed->getMaximumDiscountAmount());
        $element->addAttribute('minimum-order-amount', $mixed->getMinimumOrderAmount());
        $element->addAttribute('limit-quantity', $mixed->isLimitQuantity() ? "1" : "0");
        $element->addAttribute('quantity', $mixed->getQuantity());
        $element->addAttribute('exclude-discounted-products', $mixed->isExcludeDiscountedProducts() ? "1" : "0");
        $element->addAttribute('tax-rate', $mixed->getTaxRate() instanceof TaxRateEntity ? $mixed->getTaxRate()->getHandle() : null);
        $element->addAttribute('package', $mixed->getPackageHandle());

        return $element;
    }
}
