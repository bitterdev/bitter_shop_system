<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem;

use Bitter\BitterShopSystem\Doctrine\DiscriminatorListener;
use Bitter\BitterShopSystem\Provider\ServiceProvider;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\Http\Request;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Doctrine\DBAL\DBALException;
use Exception;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'bitter_shop_system';
    protected $appVersionRequired = '9.2.0';
    protected $pkgVersion = '3.1.4';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/BitterShopSystem' => 'Bitter\BitterShopSystem',
        'src/Bitter/BitterShopSystem/Attribute/Key' => 'Concrete\Package\BitterShopSystem\Attribute\Key'
    ];

    public function getPackageDescription(): string
    {
        return t("Powerful Shop System for ConcreteCMS.");
    }

    public function getPackageName(): string
    {
        return t("Bitter Shop System");
    }

    public function getEntityManagerProvider(): StandardPackageProvider
    {
        $locations = [
            'src/Bitter/BitterShopSystem/Entity' => 'Bitter\BitterShopSystem\Entity'
        ];

        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);

        if ($packageService->getByHandle("migration_tool") instanceof \Concrete\Core\Entity\Package) {
            $locations['src/Bitter/BitterShopSystem/MigrationTool/Entity'] = 'Bitter\BitterShopSystem\MigrationTool\Entity';
        }

        return new StandardPackageProvider($this->app, $this, $locations);
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    /**
     * @throws Exception
     */
    public function install(): \Concrete\Core\Entity\Package
    {
        $this->getEntityManager()->getEventManager()->addEventSubscriber(new DiscriminatorListener($this->getEntityManager()));

        $pkg = parent::install();
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $serviceProvider->register();

        $this->installContentFile("data.xml");

        if ($request->request->has("installSampleContent")) {
            if (is_dir($this->getPackagePath() . '/content_files')) {
                $contentImporter = new ContentImporter();
                $contentImporter->importFiles($this->getPackagePath() . '/content_files', true);
            }

            $this->installContentFile("content.xml");
        }

        if ($request->request->has("enablePublicRegistration")) {
            $config->save('concrete.user.registration.enabled', true);
            $config->save('concrete.user.registration.type', 'enabled');
        }

        return $pkg;
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("data.xml");
    }

    /**
     * @throws Exception
     */
    public function uninstall()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        try {
            $db->executeQuery("SET FOREIGN_KEY_CHECKS = 0;");
            $db->executeQuery("TRUNCATE TABLE TaxRate");
            $db->executeQuery("TRUNCATE TABLE `Order`");
            $db->executeQuery("TRUNCATE TABLE OrderPosition");
            $db->executeQuery("TRUNCATE TABLE Product");
            $db->executeQuery("TRUNCATE TABLE ShippingCost");
            $db->executeQuery("TRUNCATE TABLE Customer");
            $db->executeQuery("TRUNCATE TABLE Coupon");
            $db->executeQuery("TRUNCATE TABLE Category");
            $db->executeQuery("TRUNCATE TABLE TaxRateVariant");
            $db->executeQuery("TRUNCATE TABLE ShippingCostVariant");
            $db->executeQuery("TRUNCATE TABLE PdfEditorBlocks");
            $db->executeQuery("TRUNCATE TABLE SavedCustomerSearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedOrderSearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedProductSearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedShippingCostSearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedTaxRateSearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedCategorySearchQueries");
            $db->executeQuery("TRUNCATE TABLE SavedCouponSearchQueries");
            $db->executeQuery("TRUNCATE TABLE CustomerAttributeKeys");
            $db->executeQuery("TRUNCATE TABLE ProductAttributeKeys");
            $db->executeQuery("TRUNCATE TABLE CustomerAttributeValues");
            $db->executeQuery("TRUNCATE TABLE ProductAttributeValues");
            $db->executeQuery("TRUNCATE TABLE MultipleFilesSettings");
            $db->executeQuery("TRUNCATE TABLE MultipleFilesSelectedFiles");
            $db->executeQuery("TRUNCATE TABLE MultipleFilesValue");
            $db->executeQuery("TRUNCATE TABLE OrderCreatedNotifications");
            $db->executeQuery("TRUNCATE TABLE btCheckout");
            $db->executeQuery("TRUNCATE TABLE btProductDetails");
            $db->executeQuery("TRUNCATE TABLE btProductCategoryList");
            $db->executeQuery("TRUNCATE TABLE btProductList");
            $db->executeQuery("TRUNCATE TABLE btCart");
            $db->executeQuery("SET FOREIGN_KEY_CHECKS = 1;");
        } /** @noinspection PhpUnusedLocalVariableInspection */ catch (DBALException $e) {
            //throw new Exception(t("There was an error while truncating the data."));
        }

        parent::uninstall();
    }
}