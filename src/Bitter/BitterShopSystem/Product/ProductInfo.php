<?php /** @noinspection PhpMissingParamTypeInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Product;

use Bitter\BitterShopSystem\Attribute\Category\ProductCategory;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\ProductValue;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Foundation\ConcreteObject;
use Doctrine\ORM\EntityManagerInterface;

class ProductInfo extends ConcreteObject implements AttributeObjectInterface
{
    use ObjectTrait;

    protected $attributeCategory;
    protected $entityManager;
    /** @var Product */
    protected $entity;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductCategory $attributeCategory
    )
    {
        $this->entityManager = $entityManager;
        $this->attributeCategory = $attributeCategory;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->getEntity()->getId();
    }

    /**
     * @return Product
     */
    public function getEntity(): Product
    {
        return $this->entity;
    }

    /**
     * @param Product $entity
     * @return ProductInfo
     */
    public function setEntity($entity): ProductInfo
    {
        $this->entity = $entity;
        return $this;
    }

    public function getObjectAttributeCategory()
    {
        return $this->attributeCategory;
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = ProductKey::getByHandle($ak);
        }

        if ($ak instanceof \Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey) {
            /** @noinspection PhpParamsInspection */
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this->entity);
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new ProductValue();
            $value->setProduct($this->entity);
            $value->setAttributeKey($ak);
        }

        return $value;
    }

    /**
     * @param \Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey[] $attributes
     */
    public function saveUserAttributesForm($attributes)
    {
        foreach ($attributes as $uak) {
            $controller = $uak->getController();
            $value = $controller->createAttributeValueFromRequest();
            $this->setAttribute($uak, $value);
        }
    }
}