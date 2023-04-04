<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\API\V1;

use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class PdfEditor
{
    protected $request;
    protected $validation;
    protected $responseFactory;
    protected $blockService;
    protected $entityManager;

    public function __construct(
        Request $request,
        Validation $validation,
        ResponseFactory $responseFactory,
        BlockService $blockService,
        EntityManagerInterface $entityManager
    )
    {
        $this->request = $request;
        $this->validation = $validation;
        $this->responseFactory = $responseFactory;
        $this->blockService = $blockService;
        $this->entityManager = $entityManager;
    }

    public function removeBlock()
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        $this->validation->setData($this->request->request->all());
        $this->validation->addRequired("blockId");

        if ($this->validation->test()) {
            $blockId = (int)$this->request->request->get("blockId");

            $block = $this->blockService->getById($blockId);

            if ($block instanceof Block) {
                $this->entityManager->remove($block);
                $this->entityManager->flush();

                $editResponse->setTitle(t("Block Updated"));
                $editResponse->setMessage(t("The block has been successfully removed."));
            } else {
                $errorList->add(t("The given block id is invalid."));
            }
        } else {
            $errorList = $this->validation->getError();
        }

        $editResponse->setError($errorList);

        return $this->responseFactory->json($editResponse);
    }

    public function resizeBlock()
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        $this->validation->setData($this->request->request->all());
        $this->validation->addRequired("blockId");
        $this->validation->addRequired("top");
        $this->validation->addRequired("left");
        $this->validation->addRequired("width");
        $this->validation->addRequired("height");

        if ($this->validation->test()) {
            $blockId = (int)$this->request->request->get("blockId");
            $top = (int)$this->request->request->get("top");
            $left = (int)$this->request->request->get("left");
            $width = (int)$this->request->request->get("width");
            $height = (int)$this->request->request->get("height");

            $block = $this->blockService->getById($blockId);

            if ($block instanceof Block) {
                $block->setTop($top);
                $block->setLeft($left);
                $block->setWidth($width);
                $block->setHeight($height);

                $this->entityManager->persist($block);
                $this->entityManager->flush();

                $editResponse->setTitle(t("Block Resized"));
                $editResponse->setMessage(t("The block has been successfully resized."));
            } else {
                $errorList->add(t("The given block id is invalid."));
            }

        } else {
            $errorList = $this->validation->getError();
        }

        $editResponse->setError($errorList);

        return $this->responseFactory->json($editResponse);
    }
}