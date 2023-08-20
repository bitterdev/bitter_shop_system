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

use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Order\OrderList;

class PaymentProviderField extends AbstractField
{
    protected $requestVariables = [
        'paymentProviderHandle'
    ];

    public function getKey()
    {
        return 'paymentProviderHandle';
    }

    public function getDisplayName()
    {
        return t('Payment Provider');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var PaymentProviderService $paymentProviderService */
        $paymentProviderService = $app->make(PaymentProviderService::class);
        $list->filterByPaymentProvider($paymentProviderService->getByHandle(@$this->data['paymentProviderHandle']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        /** @var PaymentProviderService $paymentProviderService */
        $paymentProviderService = $app->make(PaymentProviderService::class);
        return $form->select('paymentProviderHandle', $paymentProviderService->getList(), @$this->data['paymentProviderHandle']);
    }
}
