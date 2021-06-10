<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Entity\Attribute\Value;

use Bitter\BitterShopSystem\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="ProductAttributeValues"
 * )
 */
class ProductValue extends AbstractValue
{
    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Bitter\BitterShopSystem\Entity\Product", inversedBy="attributes")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id")
     */
    protected $product;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ProductValue
     */
    public function setProduct(Product $product): ProductValue
    {
        $this->product = $product;
        return $this;
    }
}
