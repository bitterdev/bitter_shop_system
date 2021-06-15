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
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\User;

class Margins extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/document/margins';

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
        /** @var Validation $formValidation */
        $formValidation = $this->app->make(Validation::class);

        $editResponse = new EditResponse();

        $formValidation->setData($this->request->request->all());
        $formValidation->addRequired("top");
        $formValidation->addRequired("bottom");
        $formValidation->addRequired("left");
        $formValidation->addRequired("right");

        if ($formValidation->test()) {
            $config->save("bitter_shop_system.pdf_editor.margins.top", (int)$this->request->request->get("top"));
            $config->save("bitter_shop_system.pdf_editor.margins.bottom", (int)$this->request->request->get("bottom"));
            $config->save("bitter_shop_system.pdf_editor.margins.left", (int)$this->request->request->get("left"));
            $config->save("bitter_shop_system.pdf_editor.margins.right", (int)$this->request->request->get("right"));

            $editResponse->setMessage(t("The settings has been successfully updated."));
        } else {
            $editResponse->setError($formValidation->getError());
        }

        return $responseFactory->json($editResponse);
    }

    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $this->set("top", $config->get("bitter_shop_system.pdf_editor.margins.top", 45));
        $this->set("bottom", $config->get("bitter_shop_system.pdf_editor.margins.bottom", 45));
        $this->set("left", $config->get("bitter_shop_system.pdf_editor.margins.left", 25));
        $this->set("right", $config->get("bitter_shop_system.pdf_editor.margins.right", 20));
    }
}
