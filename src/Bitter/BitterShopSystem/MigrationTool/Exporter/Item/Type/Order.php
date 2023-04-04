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

use Bitter\BitterShopSystem\Order\OrderService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Order extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Order')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var OrderService $orderService */
        $orderService = $app->make(OrderService::class);
        $node = $element->addChild('orders');
        foreach ($collection->getItems() as $item) {
            $order = $orderService->getById($item->getItemIdentifier());
            if ($order instanceof \Bitter\BitterShopSystem\Entity\Order) {
                $this->exporter->export($order, $node);
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
        /** @var OrderService $orderService */
        $orderService = $app->make(OrderService::class);
        $order = $orderService->getById($item->getItemIdentifier());
        if ($order instanceof \Bitter\BitterShopSystem\Entity\Order) {
            return [t("Order %s", $order->getId())];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Order[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var OrderService $orderService */
        $orderService = $app->make(OrderService::class);

        foreach ($array as $id) {
            $order = $orderService->getById($id);

            if ($order instanceof \Bitter\BitterShopSystem\Entity\Order) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Order();
                $item->setItemId($order->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Order[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var OrderService $orderService */
        $orderService = $app->make(OrderService::class);
        $orders = $orderService->getAll();

        $items = [];

        foreach ($orders as $order) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Order();
            $item->setItemId($order->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'order';
    }

    public function getPluralDisplayName(): string
    {
        return t('Orders');
    }
}