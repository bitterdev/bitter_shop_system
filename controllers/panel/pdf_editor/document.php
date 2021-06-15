<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Panel\PdfEditor;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\User\User;

class Document extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/document';

    public function canAccess(): bool
    {
        $user = new User();
        return $user->isSuperUser();
    }

    public function view()
    {
    }
}
