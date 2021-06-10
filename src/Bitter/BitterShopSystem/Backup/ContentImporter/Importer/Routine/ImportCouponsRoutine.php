<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Coupon\CouponService;
use Bitter\BitterShopSystem\TaxRate\TaxRateService as TaxRateService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportCouponsRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'coupons';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CouponService $couponService */
        $couponService = $app->make(CouponService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);

        if (isset($element->coupons)) {
            foreach ($element->coupons->coupon as $item) {
                $pkg = static::getPackageObject($item['package']);

                $couponEntry = $couponService->getByCode((string)$item["code"]);

                if (!$couponEntry instanceof Coupon) {
                    $couponEntry = new Coupon;
                    $couponEntry->setCode((string)$item["code"]);
                    $couponEntry->setPackage($pkg);
                }

                $couponEntry->setValidFrom($dateService->toDateTime((string)$item["valid-from"]));
                $couponEntry->setValidTo($dateService->toDateTime((string)$item["valid-to"]));
                $couponEntry->setUsePercentageDiscount((int)$item["use-percentage-discount"] === 1);
                $couponEntry->setDiscountPrice((float)$item["discount-price"]);
                $couponEntry->setDiscountPercentage((float)$item["discount-percentage"]);
                $couponEntry->setMaximumDiscountAmount((float)$item["maximum-discount-amount"]);
                $couponEntry->setMinimumOrderAmount((float)$item["minimum-order-amount"]);
                $couponEntry->setLimitQuantity((int)$item["limit-quantity"] === 1);
                $couponEntry->setQuantity((int)$item["quantity"]);
                $couponEntry->setExcludeDiscountedProducts((int)$item["exclude-discounted-products"] === 1);
                $couponEntry->setTaxRate($taxRateService->getByHandle((string)$item["tax-rate"]));

                $entityManager->persist($couponEntry);
                $entityManager->flush();
            }
        }
    }

}
