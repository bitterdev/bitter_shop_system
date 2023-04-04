<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Entity\TaxRateVariant;
use Bitter\BitterShopSystem\TaxRate\TaxRateService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportTaxRatesRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'tax_rates';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);

        if (isset($element->taxrates)) {
            foreach ($element->taxrates->taxrate as $item) {
                $pkg = static::getPackageObject($item['package']);

                $taxRateEntry = $taxRateService->getByHandle((string)$item["handle"]);

                if (!$taxRateEntry instanceof TaxRate) {
                    $taxRateEntry = new TaxRate;
                    $taxRateEntry->setHandle((string)$item["handle"]);
                    $taxRateEntry->setPackage($pkg);
                } else {
                    foreach($taxRateEntry->getVariants() as $oldVariantEntry) {
                        $entityManager->remove($oldVariantEntry);
                    }
                }

                $taxRateEntry->setRate((float)$item["rate"]);
                $taxRateEntry->setName((string)$item["name"]);

                $entityManager->persist($taxRateEntry);

                if (isset($item->variants)) {
                    foreach ($item->variants->children() as $variant) {
                        $variantEntry = new TaxRateVariant();
                        $variantEntry->setTaxRate($taxRateEntry);
                        $variantEntry->setCountry((string)$variant["country"]);
                        $variantEntry->setState((string)$variant["state"]);
                        $variantEntry->setRate((float)$variant["tax-rate"]);
                        $entityManager->persist($variantEntry);
                    }
                }

                $entityManager->flush();
            }
        }
    }

}
