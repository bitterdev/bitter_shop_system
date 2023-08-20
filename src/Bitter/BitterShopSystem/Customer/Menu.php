<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Customer;

use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Customer $customer)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/bitter_shop_system/customers/update", $customer->getId()),
                t('Edit')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/bitter_shop_system/customers/remove", $customer->getId()),
                t('Remove'),
                [
                    "class" => "ccm-delete-item"
                ]
            )
        );
    }
}
