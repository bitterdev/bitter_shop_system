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
use Bitter\BitterShopSystem\Product\Search\Field\Field\CategoryField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\ImageField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\LocaleField;
use Bitter\BitterShopSystem\Product\Search\Field\Field\QuantityField;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Manager as FieldManager;
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
