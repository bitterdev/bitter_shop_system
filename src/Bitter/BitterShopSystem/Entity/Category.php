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

namespace Bitter\BitterShopSystem\Entity;

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Category implements ExportableInterface
{
    use PackageTrait;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $handle = '';

    /**
     * @var \Bitter\BitterShopSystem\Entity\Product[]
     * @ORM\OneToMany(targetEntity="Bitter\BitterShopSystem\Entity\Product", mappedBy="category", orphanRemoval=true)
     */
    protected $products;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Category
     */
    public function setId($id): Category
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     * @return Category
     */
    public function setHandle(string $handle): Category
    {
        $this->handle = $handle;
        return $this;
    }

    public function getExporter()
    {
        $app = Application::getFacadeApplication();
        return $app->make(\Bitter\BitterShopSystem\Export\Item\Category::class);;
    }
}