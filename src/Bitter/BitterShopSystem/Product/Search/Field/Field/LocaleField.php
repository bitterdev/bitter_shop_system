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

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Product\ProductList;

class LocaleField extends AbstractField
{
    protected $requestVariables = [
        'locale'
    ];

    public function getKey()
    {
        return 'locale';
    }

    public function getDisplayName()
    {
        return t('Locale');
    }

    /**
     * @param ProductList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByLocale(@$this->data['locale']);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var \Concrete\Core\Entity\Site\Site $site */
        $site = $app->make('site')->getActiveSiteForEditing();
        $locales = [];
        foreach ($site->getLocales() as $localeEntity) {
            $locales[$localeEntity->getLocale()] = sprintf('%s (%s)', $localeEntity->getLanguageText(), $localeEntity->getLocale());
        }
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->select('locale', $locales, @$this->data['locale']);
    }
}
