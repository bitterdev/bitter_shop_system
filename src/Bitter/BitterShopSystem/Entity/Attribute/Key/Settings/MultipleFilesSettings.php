<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Entity\Attribute\Key\Settings;

use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MultipleFilesSettings")
 */
class MultipleFilesSettings extends Settings
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $akMaxFilesCount = 1;

    public function getAttributeTypeHandle(): string
    {
        return 'multiple_files';
    }

    /**
     * @return int
     */
    public function getMaxFilesCount(): int
    {
        return $this->akMaxFilesCount;
    }

    /**
     * @param int $akMaxFilesCount
     * @return MultipleFilesSettings
     */
    public function setMaxFilesCount(int $akMaxFilesCount): MultipleFilesSettings
    {
        $this->akMaxFilesCount = $akMaxFilesCount;
        return $this;
    }

}
