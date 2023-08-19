<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PdfEditor\Block;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\PopoverMenu as CoreMenu;

class Menu extends CoreMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-pdf-editor-block-item-menu'];

    public function __construct()
    {
        parent::__construct();
        $this->addItem(new LinkItem('#', t('Edit Settings'), ['data-block-item-action' => 'edit-settings']));
        $this->addItem(new LinkItem('#', t('Remove'), ['data-block-item-action' => 'remove']));
    }
}
