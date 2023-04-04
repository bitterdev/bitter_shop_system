<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\MigrationTool\Exporter\Item\Type;

use Bitter\BitterShopSystem\Coupon\CouponService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Coupon extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Coupon')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CouponService $couponService */
        $couponService = $app->make(CouponService::class);
        $node = $element->addChild('coupons');
        foreach ($collection->getItems() as $item) {
            $coupon = $couponService->getById($item->getItemIdentifier());
            if ($coupon instanceof \Bitter\BitterShopSystem\Entity\Coupon) {
                $this->exporter->export($coupon, $node);
            }
        }
    }

    /**
     * @param ExportItem $item
     * @return array|string[]
     */
    public function getResultColumns(ExportItem $item): array
    {
        $app = Application::getFacadeApplication();
        /** @var CouponService $couponService */
        $couponService = $app->make(CouponService::class);
        $coupon = $couponService->getById($item->getItemIdentifier());
        if ($coupon instanceof \Bitter\BitterShopSystem\Entity\Coupon) {
            return [$coupon->getCode()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Coupon[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var CouponService $couponService */
        $couponService = $app->make(CouponService::class);

        foreach ($array as $id) {
            $coupon = $couponService->getById($id);

            if ($coupon instanceof \Bitter\BitterShopSystem\Entity\Coupon) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Coupon();
                $item->setItemId($coupon->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Coupon[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var CouponService $couponService */
        $couponService = $app->make(CouponService::class);
        $coupons = $couponService->getAll();

        $items = [];

        foreach ($coupons as $coupon) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Coupon();
            $item->setItemId($coupon->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'coupon';
    }

    public function getPluralDisplayName(): string
    {
        return t('Coupons');
    }
}