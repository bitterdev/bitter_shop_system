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

namespace Bitter\BitterShopSystem\TaxRate;

use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Search\ItemList\Pager\Manager\TaxRateListPagerManager;
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

class TaxRateList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t0.name', 't0.rate', 't0.handle'];
    protected $permissionsChecker = -1;
    
    public function createQuery()
    {
        $this->query->select('t0.*')
            ->from("TaxRate", "t0");
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
        $this->query->andWhere('(t0.`id` LIKE :keywords OR t0.`name` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }
    
    /**
     * @param string $name
     */
    public function filterByName($name)
    {
        $this->query->andWhere('t0.`name` LIKE :name');
        $this->query->setParameter('name', '%' . $name . '%');
    }

    /**
     * @param string $name
     */
    public function filterByHandle($handle)
    {
        $this->query->andWhere('t0.`handle` = :handle');
        $this->query->setParameter('handle', $handle);
    }
    
    /**
     * @param int $rate
     */
    public function filterByRate($rate)
    {
        $this->query->andWhere('t0.`rate` = :rate');
        $this->query->setParameter('rate', $rate);
    }
    
    
    /**
    * @param array $queryRow
    * @return TaxRate
    */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(TaxRate::class)->findOneBy(["id" => $queryRow["id"]]);
    }
    
    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
            }
        
        return -1; // unknown
    }
    
    public function getPagerManager()
    {
        return new TaxRateListPagerManager($this);
    }
    
    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }
    
    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.id)')
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
