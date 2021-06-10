<?php /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
/** @noinspection PhpMissingParamTypeInspection */
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

namespace Bitter\BitterShopSystem\Attribute\Category;

use Bitter\BitterShopSystem\Customer\CustomerInfo;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue;
use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Attribute\Category\AbstractStandardCategory;
use Concrete\Core\Entity\Attribute\Key\Key;

class CustomerCategory extends AbstractStandardCategory
{
    public function createAttributeKey(): CustomerKey
    {
        return new CustomerKey();
    }

    public function getIndexedSearchTable(): string
    {
        return 'CustomerSearchIndexAttributes';
    }

    /**
     * @param Customer $mixed
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getId();
    }

    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'customerId',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true]
                ],
            ],
            'primary' => ['customerId']
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository(CustomerKey::class);
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository(CustomerValue::class);
    }

    public function getAttributeValues($customer)
    {
        return $this->getAttributeValueRepository()->findBy([
            'customer' => $customer
        ]);
    }

    public function getAttributeValue(Key $key, $customer)
    {
        return $this->getAttributeValueRepository()->findOneBy([
            'customer' => $customer,
            'attribute_key' => $key
        ]);
    }
}
