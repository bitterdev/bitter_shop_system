<?php /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpReturnDocTypeMismatchInspection */
/** @noinspection PhpMissingParamTypeInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Product;

use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Search\ItemList\Pager\Manager\ProductListPagerManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;

class ProductList extends AttributedItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t2.name', 't2.handle', 't2.shortDescription', 't2.description', 't2.priceRegular', 't2.priceDiscounted', 't2.taxRate', 't2.shippingCost', 't2.quantity', 't2.fID', 't2.locale', 't2.category'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('t2.*')
            ->from("Product", "t2")
            ->leftJoin('t2', 'ProductSearchIndexAttributes', 'at', 't2.id = at.productId');
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        $this->query->andWhere('(t2.`id` LIKE :keywords OR t2.`name` LIKE :keywords OR t2.`handle` LIKE :keywords OR t2.`shortDescription` LIKE :keywords OR t2.`description` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * @param string $name
     */
    public function filterByName($name)
    {
        $this->query->andWhere('t2.`name` LIKE :name');
        $this->query->setParameter('name', '%' . $name . '%');
    }

    /**
     * @param int $quantity
     */
    public function filterByQuantity($quantity)
    {
        $this->query->andWhere('t2.`quantity` = :quantity');
        $this->query->setParameter('quantity', $quantity);
    }

    /**
     * @param File $fileEntity
     */
    public function filterByImage(File $fileEntity)
    {
        $this->query->andWhere('t2.`fID` = :fID');
        $this->query->setParameter('fID', $fileEntity->getFileID());
    }

    /**
     * @param string $handle
     */
    public function filterByHandle($handle)
    {
        $this->query->andWhere('t2.`handle` LIKE :handle');
        $this->query->setParameter('handle', '%' . $handle . '%');
    }

    /**
     * @param string $shortDescription
     */
    public function filterByShortDescription($shortDescription)
    {
        $this->query->andWhere('t2.`shortDescription` LIKE :shortDescription');
        $this->query->setParameter('shortDescription', '%' . $shortDescription . '%');
    }

    /**
     * @param string $description
     */
    public function filterByDescription($description)
    {
        $this->query->andWhere('t2.`description` LIKE :description');
        $this->query->setParameter('description', '%' . $description . '%');
    }

    public function filterByCurrentLocale()
    {
        if (is_object(Section::getCurrentSection())) {
            $this->filterByLocale(Section::getCurrentSection()->getLocale());
        }
    }

    public function filterByCurrentSite()
    {
        $app = Application::getFacadeApplication();
        /** @var Service $siteService */
        $siteService = $app->make(Service::class);
        $site = $siteService->getSite();
        $this->filterBySite($site);
    }

    /**
     * @param Site $site
     */
    public function filterBySite($site)
    {
        $this->query->andWhere('t2.`SiteID` = :site');
        $this->query->setParameter('site', $site->getSiteID());
    }

    /**
     * @param string $locale
     */
    public function filterByLocale($locale)
    {
        $this->query->andWhere('t2.`locale` = :locale');
        $this->query->setParameter('locale', $locale);
    }

    /**
     * @param int $priceRegular
     */
    public function filterByPriceRegular($priceRegular)
    {
        $this->query->andWhere('t2.`priceRegular` = :priceRegular');
        $this->query->setParameter('priceRegular', $priceRegular);
    }

    /**
     * @param int $priceDiscounted
     */
    public function filterByPriceDiscounted($priceDiscounted)
    {
        $this->query->andWhere('t2.`priceDiscounted` = :priceDiscounted');
        $this->query->setParameter('priceDiscounted', $priceDiscounted);
    }

    /**
     * @param TaxRate $taxRate
     */
    public function filterByTaxRate($taxRate)
    {
        if ($taxRate instanceof TaxRate) {
            $this->query->andWhere('t2.`taxRate` = :taxRate');
            $this->query->setParameter('taxRate', $taxRate->getId());
        } else {
            $this->query->andWhere('t2.`taxRate` = :taxRate');
            $this->query->setParameter('taxRate', $taxRate);
        }
    }

    /**
     * @param Category $category
     */
    public function filterByCategory($category)
    {
        $this->query->andWhere('t2.`categoryId` = :category');
        $this->query->setParameter('category', $category->getId());
    }

    /**
     * @param ShippingCost $shippingCost
     */
    public function filterByShippingCost($shippingCost)
    {
        $this->query->andWhere('t2.`shippingCostId` = :shippingCost');
        $this->query->setParameter('shippingCost', $shippingCost->getId());
    }


    /**
     * @param array $queryRow
     * @return Product
     */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(Product::class)->findOneBy(["id" => $queryRow["id"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t2.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager()
    {
        return new ProductListPagerManager($this);
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t2.id)')
                ->setMaxResults(1);
        });
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            }

            /** @noinspection PhpParamsInspection */
            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        $user = new User();
        return $user->isSuperUser();
    }

    public function setPermissionsChecker(Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function isFulltextSearch()
    {
        return $this->isFulltextSearch;
    }

    protected function getAttributeKeyClassName()
    {
        return \Bitter\BitterShopSystem\Attribute\Key\ProductKey::class;
    }
}
