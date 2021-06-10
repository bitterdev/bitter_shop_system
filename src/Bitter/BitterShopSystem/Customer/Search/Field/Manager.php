<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\Field;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Customer\Search\Field\Field\EmailField;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\BitterShopSystem\Customer\Search\Field\Field\UserField;

class Manager extends FieldManager
{
    /** @var CustomerCategory */
    protected $category;

    /**
     * @param Customer $mixed
     * @return string
     */
    public function getUser(Customer $mixed): string
    {
        if ($mixed->getUser() instanceof User) {
            return $mixed->getUser()->getUserName();
        } else {
            return '';
        }
    }
    public function __construct(
        CustomerCategory $category
    )
    {
        $this->category = $category;

        $properties = [
            new UserField(),
            new EmailField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
        $attributes = [];
        foreach ($category->getSearchableList() as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Custom Attributes'), $attributes);
    }
}
