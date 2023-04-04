<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Export\Item;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Export\Item\ItemInterface;
use Bitter\BitterShopSystem\Entity\Customer as CustomerEntity;
use Concrete\Core\Support\Facade\Application;
use SimpleXMLElement;

class Customer implements ItemInterface
{
    /**
     * @param $mixed CustomerEntity
     * @param SimpleXMLElement $element
     * @return SimpleXMLElement
     */
    public function export($mixed, SimpleXMLElement $element): SimpleXMLElement
    {
        $app = Application::getFacadeApplication();
        /** @var CategoryService $service */
        $service = $app->make(CategoryService::class);
        $categoryEntity = $service->getByHandle('customer');
        /** @var CustomerCategory $category */
        $category = $categoryEntity->getController();

        $element = $element->addChild('customer');

        $user = $mixed->getUser();

        if ($user instanceof User) {
            $username = $user->getUserName();
        } else {
            $username = null;
        }

        $element->addAttribute('email', $mixed->getEmail());
        $element->addAttribute('id', $mixed->getId());
        $element->addAttribute('user', $username);
        $element->addAttribute('package', $mixed->getPackageHandle());

        $attributes = $element->addChild('attributes');

        foreach ($category->getAttributeValues($mixed) as $av) {
            $ak = $av->getAttributeKey();
            $cnt = $ak->getController();
            $cnt->setAttributeValue($av);
            $akx = $attributes->addChild('attribute');
            $akx->addAttribute('handle', $ak->getAttributeKeyHandle());
            $cnt->exportValue($akx);
        }

        return $element;
    }
}
