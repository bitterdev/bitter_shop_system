<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Controller\Dialog\PdfEditor;

use Bitter\BitterShopSystem\Entity\PdfEditor\Block;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockService;
use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;

/** @noinspection PhpUnused */

class BlockSettings extends UserInterface
{
    protected $viewPath = '/dialogs/pdf_editor/block_settings';

    protected function canAccess(): bool
    {
        $user = new User();
        return $user->isSuperUser();
    }

    private function setDefaults()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $fontNames = $config->get("bitter_shop_system.pdf_editor.fonts", [
            "Courier" => t("Courier"),
            "Courier-Bold" => t("Courier Bold"),
            "Courier-BoldOblique" => t("Courier Bold Oblique"),
            "Courier Oblique" => t("Courier-Oblique"),
            "Helvetica" => t("Helvetica"),
            "Helvetica-Bold" => t("Helvetica Bold"),
            "Helvetica-BoldOblique" => t("Helvetica Bold Oblique"),
            "Helvetica-Oblique" => t("Helvetica Oblique"),
            "Symbol" => t("Symbol"),
            "Times-Roman" => t("Times Roman"),
            "Times-Bold" => t("Times Bold"),
            "Times-BoldItalic" => t("Times Bold Italic"),
            "Times-Italic" => t("Times Italic"),
            "ZapfDingbats" => t("Zapf Dingbats")
        ]);

        $this->set("fontNames", $fontNames);
    }

    public function submit($blockTypeHandle = null, $blockId = null)
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $editResponse = new EditResponse();
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        /** @var BlockService $blockService */
        $blockService = $this->app->make(BlockService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);

        $block = $blockService->getById((int)$blockId);

        if (!$block instanceof Block) {
            $block = new Block();
            $block->setBlockTypeHandle($blockTypeHandle);
        }

        if ($this->request->getMethod() === 'POST') {
            $formValidator->setData($this->request->request->all());
            $formValidator->addRequiredToken("edit_block_settings");
            $formValidator->addRequired("fontName");
            $formValidator->addRequired("fontSize");
            $formValidator->addRequired("fontColor");
            $formValidator->addRequired("top");
            $formValidator->addRequired("left");
            $formValidator->addRequired("width");
            $formValidator->addRequired("height");

            if ($formValidator->test()) {
                $block->setFontName($this->request->request->get("fontName"));
                $block->setFontSize($this->request->request->get("fontSize"));
                $block->setFontColor($this->request->request->get("fontColor"));
                $block->setTop((int)$this->request->request->get("top"));
                $block->setLeft((int)$this->request->request->get("left"));
                $block->setWidth((int)$this->request->request->get("width"));
                $block->setHeight((int)$this->request->request->get("height"));

                $entityManager->persist($block);
                $entityManager->flush();

                $entityManager->refresh($block);

                $configurationElement = $block->getBlockType()->getConfigurationElement();
                $configurationElement->setBlock($block);
                $elementErrors = $configurationElement->save();

                if (!$elementErrors->has()) {
                    $editResponse->setTitle(t("Block Updated"));
                    $editResponse->setMessage(t("The block has been successfully updated."));
                    $editResponse->setAdditionalDataAttribute("block", $block);
                } else {
                    $editResponse->setError($elementErrors);
                }
            } else {
                $editResponse->setError($formValidator->getError());
            }
        }

        return $responseFactory->json($editResponse);
    }

    public function add($blockTypeHandle = null)
    {
        $block = new Block();
        $block->setBlockTypeHandle($blockTypeHandle);
        $this->set("block", $block);
        $this->setDefaults();
    }

    public function edit($blockId = null)
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var BlockService $blockService */
        $blockService = $this->app->make(BlockService::class);
        $block = $blockService->getById((int)$blockId);

        if ($block instanceof Block) {
            $this->set("block", $block);
            $this->setDefaults();
        } else {
            return $responseFactory->notFound(t("Block not found"));
        }
    }
}