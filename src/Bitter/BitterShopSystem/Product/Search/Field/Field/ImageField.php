<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

namespace Bitter\BitterShopSystem\Product\Search\Field\Field;

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\File\File;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Product\ProductList;

class ImageField extends AbstractField
{
    protected $requestVariables = [
        'fID'
    ];

    public function getKey()
    {
        return 'fID';
    }

    public function getDisplayName()
    {
        return t('Image');
    }

    /**
     * @param ProductList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterByImage(File::getByID($this->data['fID']));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var FileManager $fileSelector */
        $fileSelector = $app->make(FileManager::class);
        return $fileSelector->image('fID', 'fID', t("Please select"), $this->data['fID']);
    }
}
