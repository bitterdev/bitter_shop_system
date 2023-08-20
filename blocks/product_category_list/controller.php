<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Block\ProductCategoryList;

use Bitter\BitterShopSystem\Category\CategoryList;
use Bitter\BitterShopSystem\Category\CategoryService;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Product\ProductList;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = "btProductCategoryList";
    protected $btExportPageColumns = ['productListPageId'];

    public function getBlockTypeDescription(): string
    {
        return t('Add product category list.');
    }

    public function getBlockTypeName(): string
    {
        return t('Product Category List');
    }

    public function view()
    {
        $categoryList = new CategoryList();
        $this->set('productCategories', $categoryList->getResults());
    }

    public function add()
    {
        $this->set("productListPageId", null);
    }
}
