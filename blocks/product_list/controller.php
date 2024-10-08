<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Block\ProductList;

use Bitter\BitterShopSystem\Category\CategoryService;
use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Product\ProductList;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = "btProductList";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = 0;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btExportPageColumns = ['detailsPageId'];

    public function getBlockTypeDescription(): string
    {
        return t('Add product list.');
    }

    public function getBlockTypeName(): string
    {
        return t('Product List');
    }

    public function add()
    {
        $this->set('itemsPerPage', 25);
        $this->set('detailsPageId', null);
    }

    public function action_filter_by_category($categoryHandle = null)
    {
        /** @var CategoryService $categoryService */
        $categoryService = $this->app->make(CategoryService::class);
        $category = $categoryService->getByHandle((string)$categoryHandle);
        if ($category instanceof Category) {
            $productList = new ProductList();
            $productList->setItemsPerPage((int)$this->get("itemsPerPage"));
            $productList->filterByCurrentLocale();
            $productList->filterByCategory($category);
            /** @noinspection PhpDeprecationInspection */
            $pagination = $productList->getPagination();
            $this->set('products', $productList->getResults());

            if ($pagination->haveToPaginate()) {
                $pagination = $pagination->renderView();
                $this->set('pagination', $pagination);
            }
        } else {
            $this->view();
        }
    }

    public function view()
    {
        $productList = new ProductList();
        $productList->setItemsPerPage((int)$this->get("itemsPerPage"));
        $productList->filterByCurrentLocale();
        $productList->filterByCurrentSite();
        try {
            /** @noinspection PhpDeprecationInspection */
            $pagination = $productList->getPagination();
            if ($pagination->haveToPaginate()) {
                $pagination = $pagination->renderView();
                $this->set('pagination', $pagination);
            }
        } catch (\Exception) {
            $this->set('pagination', null);
        }

        $productList->getQueryObject()->addOrderBy("t2.sortOrder");

        $this->set('products', $productList->getResults());

    }
}
