<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\MigrationTool\Exporter\Item\Type\PdfEditor;

use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Block extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Block')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var BlockService $blockService */
        $blockService = $app->make(BlockService::class);
        $node = $element->addChild('pdfeditor')->addChild('blocks');
        foreach ($collection->getItems() as $item) {
            $block = $blockService->getById($item->getItemIdentifier());
            if ($block instanceof \Bitter\BitterShopSystem\Entity\PdfEditor\Block) {
                $this->exporter->export($block, $node);
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
        /** @var BlockService $blockService */
        $blockService = $app->make(BlockService::class);
        $block = $blockService->getById($item->getItemIdentifier());
        if ($block instanceof \Bitter\BitterShopSystem\Entity\PdfEditor\Block) {
            return [t("Block %s", $block->getId())];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\PdfEditor\Block[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var BlockService $blockService */
        $blockService = $app->make(BlockService::class);

        foreach ($array as $id) {
            $block = $blockService->getById($id);

            if ($block instanceof \Bitter\BitterShopSystem\Entity\PdfEditor\Block) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\PdfEditor\Block();
                $item->setItemId($block->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\PdfEditor\Block[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var BlockService $blockService */
        $blockService = $app->make(BlockService::class);
        $blocks = $blockService->getAll();

        $items = [];

        foreach ($blocks as $block) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\PdfEditor\Block();
            $item->setItemId($block->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'pdf_editor_block';
    }

    public function getPluralDisplayName(): string
    {
        return t('Pdf Editor Blocks');
    }
}