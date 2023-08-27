<?php /** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine;

use Bitter\BitterShopSystem\Entity\Category;
use Bitter\BitterShopSystem\Category\CategoryService;
use Concrete\Core\Backup\ContentImporter\Importer\Routine\AbstractRoutine;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportCategoriesRoutine extends AbstractRoutine
{
    public function getHandle(): string
    {
        return 'categories';
    }

    public function import(SimpleXMLElement $element)
    {
        $app = Application::getFacadeApplication();
        /** @var CategoryService $categoryService */
        $categoryService = $app->make(CategoryService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Service $siteService */
        $siteService = $app->make(Service::class);
        $defaultSite = $siteService->getSite();

        if (isset($element->categories)) {
            foreach ($element->categories->category as $item) {
                $pkg = static::getPackageObject($item['package']);

                $site = $defaultSite;

                if (isset($item["site"])) {
                    $siteObject = $siteService->getByHandle($item["site"]);

                    if ($siteObject instanceof Site) {
                        $site = $siteObject;
                    }
                }

                $categoryEntry = $categoryService->getByHandle((string)$item["handle"]);

                if (!$categoryEntry instanceof Category) {
                    $categoryEntry = new Category;
                    $categoryEntry->setHandle((string)$item["handle"]);
                    $categoryEntry->setPackage($pkg);
                }

                $categoryEntry->setName((string)$item["name"]);
                $categoryEntry->setSite($site);

                $entityManager->persist($categoryEntry);
                $entityManager->flush();
            }
        }
    }

}
