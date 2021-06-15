<?php /** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Entity\PdfEditor;

use Bitter\BitterShopSystem\PdfEditor\Block\BlockType\BlockTypeInterface;
use Bitter\BitterShopSystem\PdfEditor\Block\BlockType\Manager;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="PdfEditorBlocks")
 */
class Block implements ExportableInterface, JsonSerializable
{
    use PackageTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $blockTypeHandle = 'content';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $fontName = 'Helvetica';

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $fontSize = 12;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $fontColor = "#000000";

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $content = '';

    /**
     * @var int
     * @ORM\Column(name="`left`", type="integer")
     */
    protected $left = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $top = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $width = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $height = 0;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Block
     */
    public function setId(int $id): Block
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getBlockTypeHandle(): string
    {
        return $this->blockTypeHandle;
    }

    /**
     * @param string $blockTypeHandle
     * @return Block
     */
    public function setBlockTypeHandle(string $blockTypeHandle): Block
    {
        $this->blockTypeHandle = $blockTypeHandle;
        return $this;
    }

    /**
     * @return string
     */
    public function getFontName(): string
    {
        return $this->fontName;
    }

    /**
     * @param string $fontName
     * @return Block
     */
    public function setFontName(string $fontName): Block
    {
        $this->fontName = $fontName;
        return $this;
    }

    /**
     * @return int
     */
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize
     * @return Block
     */
    public function setFontSize(int $fontSize): Block
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getFontColor(): string
    {
        return $this->fontColor;
    }

    /**
     * @param string $fontColor
     * @return Block
     */
    public function setFontColor(string $fontColor): Block
    {
        $this->fontColor = $fontColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Block
     */
    public function setContent(string $content): Block
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getLeft(): int
    {
        return $this->left;
    }

    /**
     * @param int $left
     * @return Block
     */
    public function setLeft(int $left): Block
    {
        $this->left = $left;
        return $this;
    }

    /**
     * @return int
     */
    public function getTop(): int
    {
        return $this->top;
    }

    /**
     * @param int $top
     * @return Block
     */
    public function setTop(int $top): Block
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return Block
     */
    public function setWidth(int $width): Block
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Block
     */
    public function setHeight(int $height): Block
    {
        $this->height = $height;
        return $this;
    }

    public function getBlockType(): BlockTypeInterface
    {
        $app = Application::getFacadeApplication();
        /** @var Manager $blockTypeManager */
        $blockTypeManager = $app->make(Manager::class);
        /** @var BlockTypeInterface $blockType */
        $blockType = $blockTypeManager->driver($this->getBlockTypeHandle());
        $blockType->getConfigurationElement()->setBlock($this);
        return $blockType;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\PdfEditor\Block::class);;
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "left" => $this->getLeft(),
            "top" => $this->getTop(),
            "width" => $this->getWidth(),
            "height" => $this->getHeight()
        ];
    }
}