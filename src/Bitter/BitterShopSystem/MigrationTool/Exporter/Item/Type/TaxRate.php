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

use Bitter\BitterShopSystem\TaxRate\TaxRateService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class TaxRate extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Tax Rate')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);
        $node = $element->addChild('taxrates');
        foreach ($collection->getItems() as $item) {
            $taxRate = $taxRateService->getById($item->getItemIdentifier());
            if ($taxRate instanceof \Bitter\BitterShopSystem\Entity\TaxRate) {
                $this->exporter->export($taxRate, $node);
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
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);
        $taxRate = $taxRateService->getById($item->getItemIdentifier());
        if ($taxRate instanceof \Bitter\BitterShopSystem\Entity\TaxRate) {
            return [$taxRate->getName()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\TaxRate[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);

        foreach ($array as $id) {
            $taxRate = $taxRateService->getById($id);

            if ($taxRate instanceof \Bitter\BitterShopSystem\Entity\TaxRate) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\TaxRate();
                $item->setItemId($taxRate->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\TaxRate[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);
        $taxRates = $taxRateService->getAll();

        $items = [];

        foreach ($taxRates as $taxRate) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\TaxRate();
            $item->setItemId($taxRate->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'tax_rate';
    }

    public function getPluralDisplayName(): string
    {
        return t('Tax Rates');
    }
}