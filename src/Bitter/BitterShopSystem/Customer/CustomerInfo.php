<?php /** @noinspection PhpMissingParamTypeInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpUnusedAliasInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Customer;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Foundation\ConcreteObject;
use Doctrine\ORM\EntityManagerInterface;

class CustomerInfo extends ConcreteObject implements AttributeObjectInterface
{
    use ObjectTrait;

    protected $attributeCategory;
    protected $entityManager;
    /** @var Customer */
    protected $entity;

    public function __construct(
        EntityManagerInterface $entityManager,
        CustomerCategory $attributeCategory
    )
    {
        $this->entityManager = $entityManager;
        $this->attributeCategory = $attributeCategory;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->getEntity()->getId();
    }

    /**
     * @return Customer
     */
    public function getEntity(): Customer
    {
        return $this->entity;
    }

    /**
     * @param Customer $entity
     * @return CustomerInfo
     */
    public function setEntity($entity): CustomerInfo
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
            $ak = CustomerKey::getByHandle($ak);
        }

        if ($ak instanceof \Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey) {
            /** @noinspection PhpParamsInspection */
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this->entity);
        } else {
            $value = null;
        }

        if ($value === null && $createIfNotExists) {
            $value = new CustomerValue();
            $value->setCustomer($this->entity);
            $value->setAttributeKey($ak);
        }

        return $value;
    }

    /**
     * @param \Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey[] $attributes
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