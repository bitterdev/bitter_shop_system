<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Customer\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Customer\CustomerList;
use Concrete\Core\User\User;

class UserField extends AbstractField
{
    protected $requestVariables = [
        'user'
    ];
    
    public function getKey()
    {
        return 'user';
    }
    
    public function getDisplayName()
    {
        return t('User');
    }
    
    /**
     * @param CustomerList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterByUser(User::getByUserID(@$this->data['user'])->getUserInfoObject()->getEntityObject());
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var UserSelector $userSelector */
        $userSelector = $app->make(UserSelector::class);
        return $userSelector->selectUser('user', @$this->data['user']);
    }
}
