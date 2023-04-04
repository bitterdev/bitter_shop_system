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

class PaperSize extends UserInterface
{
    protected $viewPath = '/panels/pdf_editor/document/paper_size';

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
        $formValidation->addRequired("width");
        $formValidation->addRequired("height");
        $formValidation->addRequired("pageOrientation");

        if ($formValidation->test()) {
            $config->save("bitter_shop_system.pdf_editor.paper_size.width", (int)$this->request->request->get("width"));
            $config->save("bitter_shop_system.pdf_editor.paper_size.height", (int)$this->request->request->get("height"));
            $config->save("bitter_shop_system.pdf_editor.paper_size.page_orientation", $this->request->request->get("pageOrientation"));

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

        $this->set("pageOrientations", [
            "portrait" => t("Portrait"),
            "landscape" => t("Landscape")
        ]);

        $this->set("width", $config->get("bitter_shop_system.pdf_editor.paper_size.width", 210));
        $this->set("height", $config->get("bitter_shop_system.pdf_editor.paper_size.height", 297));
        $this->set("pageOrientation", $config->get("bitter_shop_system.pdf_editor.paper_size.page_orientation", "portrait"));
    }
}
