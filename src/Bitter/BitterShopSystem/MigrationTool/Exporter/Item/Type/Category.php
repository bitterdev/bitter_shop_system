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

use Bitter\BitterShopSystem\Category\CategoryService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Category extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Category')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CategoryService $categoryService */
        $categoryService = $app->make(CategoryService::class);
        $node = $element->addChild('categories');
        foreach ($collection->getItems() as $item) {
            $category = $categoryService->getById($item->getItemIdentifier());
            if ($category instanceof \Bitter\BitterShopSystem\Entity\Category) {
                $this->exporter->export($category, $node);
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
        /** @var CategoryService $categoryService */
        $categoryService = $app->make(CategoryService::class);
        $category = $categoryService->getById($item->getItemIdentifier());
        if ($category instanceof \Bitter\BitterShopSystem\Entity\Category) {
            return [$category->getName()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Category[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var CategoryService $categoryService */
        $categoryService = $app->make(CategoryService::class);

        foreach ($array as $id) {
            $category = $categoryService->getById($id);

            if ($category instanceof \Bitter\BitterShopSystem\Entity\Category) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Category();
                $item->setItemId($category->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Category[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var CategoryService $categoryService */
        $categoryService = $app->make(CategoryService::class);
        $categories = $categoryService->getAll();

        $items = [];

        foreach ($categories as $category) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Category();
            $item->setItemId($category->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'category';
    }

    public function getPluralDisplayName(): string
    {
        return t('Categories');
    }
}