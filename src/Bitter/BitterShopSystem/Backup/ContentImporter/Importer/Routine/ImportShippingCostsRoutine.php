<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\ShippingCostVariant;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostService;
use Bitter\BitterShopSystem\TaxRate\TaxRateService as TaxRateService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportShippingCostsRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'shipping_costs';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);

        if (isset($element->shippingcosts)) {
            foreach ($element->shippingcosts->shippingcost as $item) {
                $pkg = static::getPackageObject($item['package']);

                $shippingCostEntry = $shippingCostService->getByHandle((string)$item["handle"]);

                if (!$shippingCostEntry instanceof ShippingCost) {
                    $shippingCostEntry = new ShippingCost;
                    $shippingCostEntry->setHandle((string)$item["handle"]);
                    $shippingCostEntry->setPackage($pkg);
                }else {
                    foreach($shippingCostEntry->getVariants() as $oldVariantEntry) {
                        $entityManager->remove($oldVariantEntry);
                    }
                }

                $shippingCostEntry->setPrice((float)$item["price"]);
                $shippingCostEntry->setName((string)$item["name"]);
                $shippingCostEntry->setTaxRate($taxRateService->getByHandle((string)$item["tax-rate"]));

                $entityManager->persist($shippingCostEntry);

                if (isset($item->variants)) {
                    foreach ($item->variants->children() as $variant) {
                        $variantEntry = new ShippingCostVariant();
                        $variantEntry->setShippingCost($shippingCostEntry);
                        $variantEntry->setCountry((string)$variant["country"]);
                        $variantEntry->setState((string)$variant["state"]);
                        $variantEntry->setPrice((float)$variant["price"]);
                        $entityManager->persist($variantEntry);
                    }
                }
                $entityManager->flush();
            }
        }
    }

}
