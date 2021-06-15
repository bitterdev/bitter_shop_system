<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportPdfEditorRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'pdf_editor_blocks';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Repository $config */
        $config = $app->make(Repository::class);
        /** @var ValueInspector $valueInspector */
        $inspector = $app->make('import/value_inspector');

        if (isset($element->pdfeditor)) {
            if (isset($element->pdfeditor->settings)) {

                $config->save("bitter_shop_system.pdf_editor.general.grid_size", (int)$element->pdfeditor->settings["grid-size"]);
                $config->save("bitter_shop_system.pdf_editor.general.enable_grid", ((int)$element->pdfeditor->settings["enable-grid"] === 1));
                $config->save("bitter_shop_system.pdf_editor.paper_size.width", (int)$element->pdfeditor->settings["paper-width"]);
                $config->save("bitter_shop_system.pdf_editor.paper_size.height", (int)$element->pdfeditor->settings["paper-height"]);
                $config->save("bitter_shop_system.pdf_editor.paper_size.orientation", (string)$element->pdfeditor->settings["paper-orientation"]);
                $config->save("bitter_shop_system.pdf_editor.margins.top", (int)$element->pdfeditor->settings["margin-top"]);
                $config->save("bitter_shop_system.pdf_editor.margins.bottom", (int)$element->pdfeditor->settings["margin-bottom"]);
                $config->save("bitter_shop_system.pdf_editor.margins.left", (int)$element->pdfeditor->settings["margin-left"]);
                $config->save("bitter_shop_system.pdf_editor.margins.right", (int)$element->pdfeditor->settings["margin-right"]);
                $config->save("bitter_shop_system.pdf_editor.letterhead.first_page_id", (int)$inspector->inspect((string)$element->pdfeditor->settings["letterhead-first-page"])->getReplacedValue());;
                $config->save("bitter_shop_system.pdf_editor.letterhead.following_page_id", (int)$inspector->inspect((string)$element->pdfeditor->settings["letterhead-following-page"])->getReplacedValue());;
            }

            if (isset($element->pdfeditor->blocks)) {
                foreach ($element->pdfeditor->blocks->block as $item) {
                    $pkg = static::getPackageObject($item['package']);

                    $blockEntry = new Block();
                    $blockEntry->setContent(trim((string)$item->content));
                    $blockEntry->setLeft((int)$item["left"]);
                    $blockEntry->setTop((int)$item["top"]);
                    $blockEntry->setWidth((int)$item["width"]);
                    $blockEntry->setHeight((int)$item["height"]);
                    $blockEntry->setBlockTypeHandle((string)$item["block-type-handle"]);
                    $blockEntry->setFontSize((int)$item["font-size"]);
                    $blockEntry->setFontColor((string)$item["font-color"]);
                    $blockEntry->setFontName((string)$item["font-name"]);
                    $blockEntry->setPackage($pkg);

                    $entityManager->persist($blockEntry);
                }

                $entityManager->flush();
            }
        }
    }

}
