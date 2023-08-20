<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\ColumnSet;


use Bitter\BitterShopSystem\Entity\Customer;
use Concrete\Core\Entity\User\User;

class Available extends DefaultSet
{

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
}
