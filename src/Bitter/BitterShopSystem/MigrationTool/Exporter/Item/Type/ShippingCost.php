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

use Bitter\BitterShopSystem\ShippingCost\ShippingCostService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class ShippingCost extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Shipping Cost')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);
        $node = $element->addChild('shippingcosts');
        foreach ($collection->getItems() as $item) {
            $shippingCost = $shippingCostService->getById($item->getItemIdentifier());
            if ($shippingCost instanceof \Bitter\BitterShopSystem\Entity\ShippingCost) {
                $this->exporter->export($shippingCost, $node);
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
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);
        $shippingCost = $shippingCostService->getById($item->getItemIdentifier());
        if ($shippingCost instanceof \Bitter\BitterShopSystem\Entity\ShippingCost) {
            return [$shippingCost->getName()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\ShippingCost[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);

        foreach ($array as $id) {
            $shippingCost = $shippingCostService->getById($id);

            if ($shippingCost instanceof \Bitter\BitterShopSystem\Entity\ShippingCost) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\ShippingCost();
                $item->setItemId($shippingCost->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\ShippingCost[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);
        $shippingCosts = $shippingCostService->getAll();

        $items = [];

        foreach ($shippingCosts as $shippingCost) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\ShippingCost();
            $item->setItemId($shippingCost->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'shipping_cost';
    }

    public function getPluralDisplayName(): string
    {
        return t('Shipping Costs');
    }
}