<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\ColumnSet;


use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\CategoryColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\DescriptionColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\IdColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ImageColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\QuantityColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\ShortDescriptionColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\HandleColumn;
use Bitter\BitterShopSystem\Product\Search\ColumnSet\Column\SortOrderColumn;
use Bitter\BitterShopSystem\Transformer\MoneyTransformer;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Support\Facade\Application;

class Available extends DefaultSet
{

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

    public function getLocale(Product $mixed): ?string
    {
        $app = Application::getFacadeApplication();
        /** @var \Concrete\Core\Entity\Site\Site $site */
        $site = $app->make('site')->getActiveSiteForEditing();

        foreach ($site->getLocales() as $localeEntity) {
            if ($localeEntity->getLocale() === $mixed->getLocale()) {
                return sprintf('%s (%s)', $localeEntity->getLanguageText(), $localeEntity->getLocale());;
            }
        }

        return null;
    }
    public function __construct()
    {
        parent::__construct();

        $this->addColumn(new IdColumn());
        $this->addColumn(new HandleColumn());
        $this->addColumn(new ShortDescriptionColumn());
        $this->addColumn(new DescriptionColumn());
        $this->addColumn(new QuantityColumn());
        $this->addColumn(new SortOrderColumn());
        $this->addColumn(new ImageColumn());
        $this->addColumn(new CategoryColumn());
    }
}
