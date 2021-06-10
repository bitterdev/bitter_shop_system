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

use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Product\ProductInfo;
use Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\ProductValue;
use Concrete\Core\Attribute\Category\AbstractStandardCategory;
use Concrete\Core\Entity\Attribute\Key\Key;

class ProductCategory extends AbstractStandardCategory
{
    public function createAttributeKey(): ProductKey
    {
        return new ProductKey();
    }

    public function getIndexedSearchTable(): string
    {
        return 'ProductSearchIndexAttributes';
    }

    /**
     * @param Product $mixed
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
                    'name' => 'productId',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true]
                ],
            ],
            'primary' => ['productId']
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository(ProductKey::class);
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository(ProductValue::class);
    }

    public function getAttributeValues($product)
    {
        return $this->getAttributeValueRepository()->findBy([
            'product' => $product
        ]);
    }

    public function getAttributeValue(Key $key, $product)
    {
        return $this->getAttributeValueRepository()->findOneBy([
            'product' => $product,
            'attribute_key' => $key
        ]);
    }
}
