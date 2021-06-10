<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\MigrationTool\Exporter\Item\Type;

use Bitter\BitterShopSystem\Customer\CustomerService;
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ExportItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Export\ObjectCollection;
use PortlandLabs\Concrete5\MigrationTool\Exporter\Item\Type\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use SimpleXMLElement;

class Customer extends AbstractType
{
    public function getHeaders(): array
    {
        return [t('Customer')];
    }

    public function exportCollection(ObjectCollection $collection, SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        $node = $element->addChild('customers');
        foreach ($collection->getItems() as $item) {
            $customer = $customerService->getById($item->getItemIdentifier());
            if ($customer instanceof \Bitter\BitterShopSystem\Entity\Customer) {
                $this->exporter->export($customer, $node);
            }
        }
    }

    /**
     * @param ExportItem $item
     * @return array|string[]
     */
    public function getResultColumns(ExportItem $item): array
    {
        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        $customer = $customerService->getById($item->getItemIdentifier());
        if ($customer instanceof \Bitter\BitterShopSystem\Entity\Customer) {
            return [$customer->getEmail()];
        } else {
            return [""];
        }
    }

    /**
     * @param $array
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Customer[]
     */
    public function getItemsFromRequest($array): array
    {
        $items = [];

        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);

        foreach ($array as $id) {
            $customer = $customerService->getById($id);

            if ($customer instanceof \Bitter\BitterShopSystem\Entity\Customer) {
                $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Customer();
                $item->setItemId($customer->getId());
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param Request $request
     * @return \Bitter\BitterShopSystem\MigrationTool\Entity\Customer[]
     */
    public function getResults(Request $request): array
    {
        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        $customers = $customerService->getAll();

        $items = [];

        foreach ($customers as $customer) {
            $item = new \Bitter\BitterShopSystem\MigrationTool\Entity\Customer();
            $item->setItemId($customer->getId());
            $items[] = $item;
        }

        return $items;
    }

    public function getHandle(): string
    {
        return 'customer';
    }

    public function getPluralDisplayName(): string
    {
        return t('Customers');
    }
}