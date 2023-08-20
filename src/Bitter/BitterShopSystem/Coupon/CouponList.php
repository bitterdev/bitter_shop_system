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

namespace Bitter\BitterShopSystem\Coupon;

use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Search\ItemList\Pager\Manager\CouponListPagerManager;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;

class CouponList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t5.code', 't5.validFrom', 't5.validTo', 't5.usePercentageDiscount', 't5.discountPrice', 't5.discountPercentage', 't5.maximumDiscountAmount', 't5.minimumOrderAmount', 't5.limitQuantity', 't5.quantity', 't5.excludeDiscountedProducts'];
    protected $permissionsChecker = -1;
    
    public function createQuery()
    {
        $this->query->select('t5.*')
            ->from("Coupon", "t5");
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
        $this->query->andWhere('(t5.`id` LIKE :keywords OR t5.`code` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }
    
    /**
     * @param string $code
     */
    public function filterByCode($code)
    {
        $this->query->andWhere('t5.`code` LIKE :code');
        $this->query->setParameter('code', '%' . $code . '%');
    }

    /**
     * @param float $discountPrice
     */
    public function filterByDiscountPrice($discountPrice)
    {
        $this->query->andWhere('t5.`discountPrice` = :discountPrice');
        $this->query->setParameter('discountPrice', $discountPrice);
    }

    /**
     * @param float $discountPercentage
     */
    public function filterByDiscountPercentage($discountPercentage)
    {
        $this->query->andWhere('t5.`discountPercentage` = :discountPercentage');
        $this->query->setParameter('discountPercentage', $discountPercentage);
    }

    /**
     * @param float $maximumDiscountAmount
     */
    public function filterByMaximumDiscountAmount($maximumDiscountAmount)
    {
        $this->query->andWhere('t5.`maximumDiscountAmount` = :maximumDiscountAmount');
        $this->query->setParameter('maximumDiscountAmount', $maximumDiscountAmount);
    }

    /**
     * @param float $minimumOrderAmount
     */
    public function filterByMinimumOrderAmount($minimumOrderAmount)
    {
        $this->query->andWhere('t5.`minimumOrderAmount` = :minimumOrderAmount');
        $this->query->setParameter('minimumOrderAmount', $minimumOrderAmount);
    }

    /**
     * @param int $quantity
     */
    public function filterByQuantity($quantity)
    {
        $this->query->andWhere('t5.`quantity` = :quantity');
        $this->query->setParameter('quantity', $quantity);
    }

    /**
     * @param TaxRate $taxRate
     */
    public function filterByTaxRate($taxRate)
    {
        if ($taxRate instanceof TaxRate) {
            $this->query->andWhere('t5.`taxRateId` = :taxRate');
            $this->query->setParameter('taxRate', $taxRate->getId());
        } else {
            $this->query->andWhere('t5.`taxRateId` = :taxRate');
            $this->query->setParameter('taxRate', $taxRate);
        }
    }

    public function filterByExcludeDiscountedProducts(bool $excludeDiscountedProducts)
    {
        if ($excludeDiscountedProducts) {
            $this->query->andWhere('t5.`excludeDiscountedProducts` = 1');
        }
    }

    public function filterByUsePercentageDiscount(bool $usePercentageDiscount)
    {
        if ($usePercentageDiscount) {
            $this->query->andWhere('t5.`usePercentageDiscount` = 1');
        }
    }

    public function filterByLimitQuantity(bool $limitQuantity)
    {
        if ($limitQuantity) {
            $this->query->andWhere('t5.`limitQuantity` = 1');
        }
    }

    /**
     * @param string $date
     * @param string $comparison
     */
    public function filterByValidFrom($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t5.validFrom', $comparison,
            $this->query->createNamedParameter($date)));
    }

    /**
     * @param string $date
     * @param string $comparison
     */
    public function filterByValidTo($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t5.validTo', $comparison,
            $this->query->createNamedParameter($date)));
    }
    
    /**
    * @param array $queryRow
    * @return Coupon
    */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(Coupon::class)->findOneBy(["id" => $queryRow["id"]]);
    }
    
    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t5.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
            }
        
        return -1; // unknown
    }
    
    public function getPagerManager()
    {
        return new CouponListPagerManager($this);
    }
    
    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }
    
    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t5.id)')
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
}
