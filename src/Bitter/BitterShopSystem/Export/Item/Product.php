<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */
/** @noinspection DuplicatedCode */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Export\Item;

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Export\Item\ItemInterface;
use Bitter\BitterShopSystem\Entity\Product as ProductEntity;
use Bitter\BitterShopSystem\Entity\TaxRate as TaxRateEntity;
use Bitter\BitterShopSystem\Entity\ShippingCost as ShippingCostEntity;
use Concrete\Core\Support\Facade\Application;
use SimpleXMLElement;

class Product implements ItemInterface
{
    /**
     * @param $mixed ProductEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $app = Application::getFacadeApplication();
        /** @var CategoryService $service */
        $service = $app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('Product');
        /** @var ProductCategory $category */
        $category = $categoryEntity->getController();

        $element = $element->addChild('product');

        $element->addAttribute('name', $mixed->getName());
        $element->addAttribute('handle', $mixed->getHandle());
        $element->addAttribute('tax-rate', $mixed->getTaxRate() instanceof TaxRateEntity ? $mixed->getTaxRate()->getHandle() : null);
        $element->addAttribute('shipping-cost', $mixed->getShippingCost() instanceof ShippingCostEntity ? $mixed->getShippingCost()->getHandle() : null);
        $element->addAttribute('category', $mixed->getCategory() instanceof \Bitter\BitterShopSystem\Entity\Category ? $mixed->getCategory()->getHandle() : null);
        $element->addAttribute('price-regular', $mixed->getPriceRegular());
        $element->addAttribute('price-discounted', $mixed->getPriceDiscounted());
        $element->addAttribute('locale', $mixed->getLocale());
        $element->addAttribute('package', $mixed->getPackageHandle());

        $element->addChild('image', $mixed->getImage() instanceof File ? ContentExporter::replaceFileWithPlaceHolder($mixed->getImage()->getFileID()) : null);

        $cnode = $element->addChild('description');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDataSection($mixed->getDescription()));

        $cnode = $element->addChild('short-description');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDataSection($mixed->getShortDescription()));

        $attributes = $element->addChild('attributes');

        foreach ($category->getAttributeValues($mixed) as $av) {
            $ak = $av->getAttributeKey();
            $cnt = $ak->getController();
            $cnt->setAttributeValue($av);
            $akx = $attributes->addChild('attribute');
            $akx->addAttribute('handle', $ak->getAttributeKeyHandle());
            $cnt->exportValue($akx);
        }

        return $element;
    }
}
