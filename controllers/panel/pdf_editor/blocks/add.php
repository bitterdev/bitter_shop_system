<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Panel\PdfEditor\Blocks;

use Bitter\BitterShopSystem\PdfEditor\Block\BlockType\Manager;
use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\User\User;

class Add extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/blocks/add';

    public function canAccess(): bool
    {
        $user = new User();
        return $user->isSuperUser();
    }

    public function view()
    {
        /** @var Manager $blockTypeManager */
        $blockTypeManager = $this->app->make(Manager::class);
        $this->set("blockTypes", $blockTypeManager->getDrivers());
    }
}
