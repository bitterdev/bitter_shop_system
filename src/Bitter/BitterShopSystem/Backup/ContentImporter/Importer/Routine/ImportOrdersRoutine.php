<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Entity\OrderPosition;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Order\OrderService;
use Bitter\BitterShopSystem\Product\ProductService as ProductService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportOrdersRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'orders';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var OrderService $orderService */
        $orderService = $app->make(OrderService::class);
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Date $dateService */
        $dateService = $app->make(Date::class);

        if (isset($element->orders)) {
            foreach ($element->orders->order as $item) {
                $pkg = static::getPackageObject($item['package']);

                $orderEntry = $orderService->getById((string)$item["id"]);

                if (!$orderEntry instanceof Order) {
                    $orderEntry = new Order;
                    $orderEntry->setId((string)$item["id"]);
                    $orderEntry->setPackage($pkg);
                } else {
                    foreach ($orderEntry->getOrderPositions() as $oldOrderPosition) {
                        $entityManager->remove($oldOrderPosition);
                    }
                }

                $orderEntry->setTransactionId((string)$item["transaction-id"]);
                $orderEntry->setTotal((float)$item["total"]);
                $orderEntry->setTax((float)$item["tax"]);
                $orderEntry->setSubtotal((float)$item["subtotal"]);
                $orderEntry->setPaymentProviderHandle((float)$item["payment-provider-handle"]);
                $orderEntry->setPaymentReceived((bool)$item["payment-deceived"]);
                $orderEntry->setPaymentReceivedDate($dateService->toDateTime((string)$item["payment-deceived-date"]));
                $orderEntry->setOrderDate($dateService->toDateTime((string)$item["order-date"]));

                $entityManager->persist($orderEntry);

                if (isset($item->positions)) {
                    foreach ($item->positions->children() as $position) {
                        $product = $productService->getByHandleWithCurrentLocale((string)$position["product-handle"]);

                        $orderPositionEntry = new OrderPosition();

                        if ($product instanceof Product) {
                            $orderPositionEntry->setProduct($product);
                        }

                        $orderPositionEntry->setQuantity((int)$position["quantity"]);
                        $orderPositionEntry->setDescription((string)$position["description"]);
                        $orderPositionEntry->setPrice((float)$position["price"]);
                        $orderPositionEntry->setTax((float)$position["tax"]);
                        $orderPositionEntry->setOrder($orderEntry);

                        $entityManager->persist($orderPositionEntry);
                    }
                }

                $entityManager->flush();
            }
        }
    }

}
