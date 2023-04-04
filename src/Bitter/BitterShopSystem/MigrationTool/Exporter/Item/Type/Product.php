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

use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Product extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Product')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);
        $node = $element->addChild('products');
        foreach ($collection->getItems() as $item) {
            $product = $productService->getById($item->getItemIdentifier());
            if ($product instanceof \Bitter\BitterShopSystem\Entity\Product) {
                $this->exporter->export($product, $node);
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
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);
        $product = $productService->getById($item->getItemIdentifier());
        if ($product instanceof \Bitter\BitterShopSystem\Entity\Product) {
            return [$product->getName()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Product[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);

        foreach ($array as $id) {
            $product = $productService->getById($id);

            if ($product instanceof \Bitter\BitterShopSystem\Entity\Product) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Product();
                $item->setItemId($product->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Product[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);
        $products = $productService->getAll();

        $items = [];

        foreach ($products as $product) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Product();
            $item->setItemId($product->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'product';
    }

    public function getPluralDisplayName(): string
    {
        return t('Products');
    }
}