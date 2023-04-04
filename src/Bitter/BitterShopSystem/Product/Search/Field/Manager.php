<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\Field;

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Product\Search\Field\Field\CategoryField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\ImageField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\LocaleField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\QuantityField;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\BitterShopSystem\Entity\Product;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\PersistentCollection;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Product\Search\Field\Field\NameField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\HandleField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\ShortDescriptionField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\DescriptionField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\PriceRegularField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\PriceDiscountedField;

class Manager extends FieldManager
{
    /** @var ProductCategory */
    protected $category;

    public function getPriceRegular(Product $product)
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($product->getPriceRegular());
    }

    public function getPriceDiscounted(Product $product)
    {
        $app = Application::getFacadeApplication();
        /** @var MoneyTransformer $moneyTransformer */
        $moneyTransformer = $app->make(MoneyTransformer::class);
        return $moneyTransformer->transform($product->getPriceDiscounted());
    }

    /**
     * @param Product $mixed
     * @return string
     */
    public function getTaxRate(Product $mixed): string
    {
        if ($mixed->getTaxRate() instanceof TaxRate) {
            return $mixed->getTaxRate()->getName();
        } else {
            return '';
        }
    }

    /**
     * @param Product $mixed
     * @return string
     */
    public function getShippingCost(Product $mixed): string
    {
        if ($mixed->getShippingCost() instanceof ShippingCost) {
            return $mixed->getShippingCost()->getName();
        } else {
            return '';
        }
    }

    /**
     * @param Product $mixed
     * @return string
     */
    public function getCategory(Product $mixed): string
    {
        if ($mixed->getCategory() instanceof Category) {
            return $mixed->getCategory()->getName();
        } else {
            return '';
        }
    }

    public function getImage(Product $mixed): string
    {
        if ($mixed->getImage() instanceof File &&
            $mixed->getImage()->getApprovedVersion() instanceof Version) {
            return $mixed->getImage()->getApprovedVersion()->getFileName();
        } else {
            return '';
        }
    }

    public function getLocale(Product $mixed): string
    {
        $app = Application::getFacadeApplication();
        /** @var \Concrete\Core\Entity\Site\Site $site */
        $site = $app->make('site')->getActiveSiteForEditing();

        foreach ($site->getLocales() as $localeEntity) {
            if ($localeEntity->getLocale() === $mixed->getLocale()) {
                return sprintf('%s (%s)', $localeEntity->getLanguageText(), $localeEntity->getLocale());;
            }
        }
    }

    public function __construct(
        ProductCategory $category
    )
    {
        $this->category = $category;

        $properties = [
            new NameField(),
            new HandleField(),
            new ShortDescriptionField(),
            new DescriptionField(),
            new PriceRegularField(),
            new PriceDiscountedField(),
            new QuantityField(),
            new ImageField(),
            new LocaleField(),
            new CategoryField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
        $attributes = [];
        foreach ($category->getSearchableList() as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Custom Attributes'), $attributes);
    }
}
