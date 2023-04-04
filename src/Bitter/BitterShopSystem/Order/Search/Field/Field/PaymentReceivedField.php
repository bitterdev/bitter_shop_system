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

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Order\OrderList;
use HtmlObject\Element;

class PaymentReceivedField extends AbstractField
{
    protected $requestVariables = [
        'paymentReceived'
    ];

    public function getKey()
    {
        return 'paymentReceived';
    }

    public function getDisplayName()
    {
        return t('Payment Received');
    }

    /**
     * @param OrderList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByPaymentReceived(isset($this->data['paymentReceived']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $checkbox = new Element("div", new Element("label", $form->checkbox('paymentReceived', 1, isset($this->data['paymentReceived'])) . t("Payment Received")), ["class" => "checkbox"]);
        return (string)$checkbox->render();
    }
}
