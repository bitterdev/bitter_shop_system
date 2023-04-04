<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Panel\PdfEditor\Document;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\User;

class Letterhead extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/document/letterhead';

    public function canAccess(): bool
    {
        $user = new User();
        return $user->isSuperUser();
    }

    public function submit()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);

        $editResponse = new EditResponse();

        $config->save("bitter_shop_system.pdf_editor.letterhead.first_page_id", (int)$this->request->request->get("firstPageId"));
        $config->save("bitter_shop_system.pdf_editor.letterhead.following_page_id", (int)$this->request->request->get("followingPageId"));

        $editResponse->setMessage(t("The settings has been successfully updated."));

        return $responseFactory->json($editResponse);
    }

    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $this->set("firstPageId", $config->get("bitter_shop_system.pdf_editor.letterhead.first_page_id"));
        $this->set("followingPageId", $config->get("bitter_shop_system.pdf_editor.letterhead.following_page_id"));
    }
}
