<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Order\Search\Field\Field;

use Bitter\BitterShopSystem\Customer\CustomerService;
use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Order\OrderList;

class CustomerField extends AbstractField
{
    protected $requestVariables = [
        'customerId'
    ];

    public function getKey()
    {
        return 'customerId';
    }

    public function getDisplayName()
    {
        return t('Customer');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        $list->filterByCustomer($customerService->getById((int)@$this->data['customerId']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        /** @var CustomerService $customerService */
        $customerService = $app->make(CustomerService::class);
        return $form->select('customerId', $customerService->getList(), @$this->data['customerId']);
    }
}
