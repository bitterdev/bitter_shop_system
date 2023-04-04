<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Entity\Attribute\Value\Value;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\FileProviderInterface;
use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="MultipleFilesValue")
 */
class MultipleFilesValue extends AbstractValue implements FileProviderInterface
{
    /**
     * @var ArrayCollection|MultipleFilesSelectedFiles[]
     * @ORM\OneToMany(targetEntity="\Bitter\BitterShopSystem\Entity\Attribute\Value\Value\MultipleFilesSelectedFiles", cascade={"persist", "remove"}, mappedBy="value")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getSelectedFiles()
    {
        return $this->files;
    }

    public function setSelectedFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return File[]|array
     */
    public function getFileObjects(): array
    {
        $files = array();
        $values = $this->getSelectedFiles();
        if ($values->count()) {
            foreach ($values as $f) {
                $files[] = $f->getFile();
            }
        }

        return $files;
    }
}
