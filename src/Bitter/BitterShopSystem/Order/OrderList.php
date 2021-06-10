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

namespace Bitter\BitterShopSystem\Order;

use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\Search\ItemList\Pager\Manager\OrderListPagerManager;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;

class OrderList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t4.id', 't4.transactionId', 't4.total', 't4.tax', 't4.subtotal', 't4.paymentReceived', 't4.paymentReceivedDate', 't4.paymentProviderHandle', 't4.orderDate', 't4.customerId'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('t4.*')
            ->from("`Order`", "t4");
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    public function filterByCustomer(?Customer $customer)
    {
        $this->query->andWhere('t4.`customerId` = :customerId');
        $this->query->setParameter('customerId', $customer->getId());
    }

    public function filterByPaymentProvider(?PaymentProviderInterface $paymentProvider)
    {
        $this->query->andWhere('t4.`paymentProviderHandle` = :paymentProviderHandle');
        $this->query->setParameter('paymentProviderHandle', $paymentProvider->getHandle());
    }

    public function filterByTransactionId(int $transactionId)
    {
        $this->query->andWhere('t4.`transactionId` = :transactionId');
        $this->query->setParameter('transactionId', $transactionId);
    }

    public function filterByTotal(int $from, int $to)
    {
        $this->query->andWhere('t4.`total` >= :totalFrom');
        $this->query->andWhere('t4.`total` <= :totalTo');
        $this->query->setParameter('totalFrom', $from);
        $this->query->setParameter('totalTo', $to);
    }

    public function filterByPaymentReceived(bool $isPaymentReceived)
    {
        if ($isPaymentReceived) {
            $this->query->andWhere('t4.`paymentReceived` = 1');
        }
    }

    public function filterBySubtotal(int $from, int $to)
    {
        $this->query->andWhere('t4.`subtotal` >= :subtotalFrom');
        $this->query->andWhere('t4.`subtotal` <= :subtotalTo');
        $this->query->setParameter('subtotalFrom', $from);
        $this->query->setParameter('subtotalTo', $to);
    }

    public function filterByTax(int $from, int $to)
    {
        $this->query->andWhere('t4.`tax` >= :taxFrom');
        $this->query->andWhere('t4.`tax` <= :taxTo');
        $this->query->setParameter('taxFrom', $from);
        $this->query->setParameter('taxTo', $to);
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        $this->query->andWhere('(t4.`id` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * @param string $date
     * @param string $comparison
     */
    public function filterByOrderDate($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t4.orderDate', $comparison,
            $this->query->createNamedParameter($date)));
    }

    /**
     * @param string $date
     * @param string $comparison
     */
    public function filterByPaymentReceivedDate($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t4.paymentReceivedDate', $comparison,
            $this->query->createNamedParameter($date)));
    }

    /**
     * @param array $queryRow
     * @return Order
     */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(Order::class)->findOneBy(["id" => $queryRow["id"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t4.id)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager()
    {
        return new OrderListPagerManager($this);
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t4.id)')
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
