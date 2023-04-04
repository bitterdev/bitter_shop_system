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

class General extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/document/general';

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
        $formValidation->addRequired("gridSize");

        if ($formValidation->test()) {
            $config->save("bitter_shop_system.pdf_editor.general.enable_grid", $this->request->request->has("enableGrid"));
            $config->save("bitter_shop_system.pdf_editor.general.grid_size", (int)$this->request->request->get("gridSize"));

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

        $this->set("enableGrid", $config->get("bitter_shop_system.pdf_editor.general.enable_grid", true));
        $this->set("gridSize", $config->get("bitter_shop_system.pdf_editor.general.grid_size", 5));
    }
}
