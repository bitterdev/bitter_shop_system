<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductService;
use Bitter\BitterShopSystem\TaxRate\TaxRateService as TaxRateService;
use Bitter\BitterShopSystem\ShippingCost\ShippingCostService as ShippingCostService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspector;
use Concrete\Core\File\File;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportProductsRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'products';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var ProductService $productService */
        $productService = $app->make(ProductService::class);
        /** @var TaxRateService $taxRateService */
        $taxRateService = $app->make(TaxRateService::class);
        /** @var ShippingCostService $shippingCostService */
        $shippingCostService = $app->make(ShippingCostService::class);
        /** @var \Bitter\BitterShopSystem\Category\CategoryService $categoryService */
        $categoryService = $app->make(\Bitter\BitterShopSystem\Category\CategoryService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var ValueInspector $valueInspector */
        $valueInspector = $app->make('import/value_inspector');
        /** @var \Concrete\Core\Entity\Site\Site $site */
        $site = $app->make('site')->getActiveSiteForEditing();
        $defaultLocale = "";
        $locales = [];
        foreach ($site->getLocales() as $localeEntity) {
            $locales[] = $localeEntity->getLocale();
            if ($localeEntity->getIsDefault()) {
                $defaultLocale = $localeEntity->getLocale();
            }
        }

        if (isset($element->products)) {
            foreach ($element->products->product as $item) {
                $pkg = static::getPackageObject($item['package']);

                $locale = (string)$item["locale"];

                if (!in_array($locale, $locales)) {
                    $locale = $defaultLocale;
                }

                $productEntry = $productService->getByHandleWithLocale((string)$item["handle"], $locale);

                if (!$productEntry instanceof Product) {
                    $productEntry = new Product();
                    $productEntry->setHandle((string)$item["handle"]);
                    $productEntry->setPackage($pkg);
                }

                $productEntry->setName((string)$item["name"]);
                $productEntry->setPriceRegular((float)$item["price-regular"]);
                $productEntry->setPriceDiscounted((float)$item["price-discounted"]);
                $productEntry->setQuantity((int)$item["quantity"]);
                $productEntry->setLocale($locale);
                $productEntry->setShippingCost($shippingCostService->getByHandle((string)$item["shipping-cost"]));
                $productEntry->setTaxRate($taxRateService->getByHandle((string)$item["tax-rate"]));
                $productEntry->setCategory($categoryService->getByHandle((string)$item["category"]));
                $productEntry->setShortDescription(trim((string)$item->shortdescription));
                $productEntry->setDescription(trim((string)$item->description));
                $productEntry->setImage(File::getByID($valueInspector->inspect((string)$item->image)->getReplacedValue()));

                $entityManager->persist($productEntry);
                $entityManager->flush();

                if (isset($item->attributes)) {
                    foreach ($item->attributes->children() as $attr) {
                        $handle = (string)$attr['handle'];
                        $ak = ProductKey::getByHandle($handle);
                        if (is_object($ak)) {
                            $value = $ak->getController()->importValue($attr);
                            $productEntry->setAttribute($handle, $value);
                        }
                    }
                }
            }
        }
    }

}
