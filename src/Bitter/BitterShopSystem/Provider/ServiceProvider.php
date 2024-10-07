<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Provider;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Attribute\Category\Manager;
use Bitter\BitterShopSystem\Attribute\Key\ProductKey;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportCategoriesRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportCouponsRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportCustomersRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportOrdersRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportPdfEditorRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportProductsRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportShippingCostsRoutine;
use Bitter\BitterShopSystem\Backup\ContentImporter\Importer\Routine\ImportTaxRatesRoutine;
use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Customer\CustomerService as CustomerService;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\MigrationTool\Exporter\Item\Type\Manager as ExportManager;
use Bitter\BitterShopSystem\Routing\RouteList;
use Concrete\Core\Application\Application;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Backup\ContentImporter\Importer\Manager as ImporterManager;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Package\ItemCategory\Manager as CorePackageItemCategoryManager;
use Bitter\BitterShopSystem\Package\ItemCategory\Manager as PackageItemCategoryManager;
use Concrete\Package\BitterShopSystem\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServiceProvider extends Provider
{
    protected $router;
    protected $eventDispatcher;
    protected $cartService;
    protected $packageService;
    /** @var Controller */
    protected $pkg;

    public function __construct(
        Application     $app,
        RouterInterface $router,
        EventDispatcher $eventDispatcher,
        CheckoutService $cartService,
        PackageService  $packageService
    )
    {
        parent::__construct($app);

        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartService = $cartService;
        $this->packageService = $packageService;
        $this->pkg = $this->packageService->getByHandle("bitter_shop_system")->getController();
    }

    public function register()
    {
        $this->initializeAutoloader();
        $this->registerRoutes();
        $this->overrideAttributeCategoryManager();
        $this->overridePackageItemCategoryManager();
        $this->initializeSearchProviders();
        $this->addImporterRoutines();
        $this->registerEventHandlers();
        $this->overrideExporterCategoryManager();
        $this->overrideNotificationManager();
        $this->registerAssets();
        $this->applyCoreFixes();
        $this->overrideDiscriminatorMap();
    }

    /**
     * @throws MappingException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws ReflectionException
     * @throws BindingResolutionException
     */
    private function overrideDiscriminatorMap(): void
    {
        /** @var EntityManagerInterface $entityManager */
        /** @noinspection PhpUnhandledExceptionInspection */
        $entityManager = $this->app->make(EntityManagerInterface::class);

        $metaData = $entityManager->getMetadataFactory()->getMetadataFor(Key::class);

        $metaData->addDiscriminatorMapClass("productkey", \Bitter\BitterShopSystem\Entity\Attribute\Key\ProductKey::class);
        $metaData->addDiscriminatorMapClass("customerkey", \Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey::class);

        $entityManager->getMetadataFactory()->setMetadataFor(Key::class, $metaData);
    }

    private function applyCoreFixes()
    {
        $this->app->bind(
            '\Concrete\Package\BitterShopSystem\Attribute\Key\ProductKey',
            ProductKey::class
        );

        $this->app->bind(
            '\Concrete\Package\BitterShopSystem\Attribute\Key\CustomerKey',
            \Bitter\BitterShopSystem\Attribute\Key\CustomerKey::class
        );
    }

    private function registerAssets()
    {
        $assetList = AssetList::getInstance();
        $assetList->register("javascript", "bitter_shop_system/pdf_editor", "js/pdf_editor.js", ["minify" => false, "combine" => false], $this->pkg);
        $assetList->register("javascript-localized", "bitter_shop_system/pdf_editor", "/ccm/assets/localization/bitter_shop_system/js", [], $this->pkg);
        $assetList->register("css", "bitter_shop_system/pdf_editor", "css/pdf_editor.css", ["minify" => false], $this->pkg);
        $assetList->registerGroup("bitter_shop_system/pdf_editor", [
            ["javascript", "bitter_shop_system/pdf_editor"],
            ["javascript-localized", "bitter_shop_system/pdf_editor"],
            ["css", "bitter_shop_system/pdf_editor"]
        ]);
    }

    private function overrideNotificationManager()
    {
        $this->app->singleton("Concrete\Core\Notification\Type\Manager", function ($app) {
            $manager = new \Bitter\BitterShopSystem\Notification\Type\Manager($app);
            $manager->driver('core_update');
            $manager->driver('new_conversation_message');
            $manager->driver('new_form_submission');
            $manager->driver('new_private_message');
            $manager->driver('user_signup');
            $manager->driver('workflow_progress');
            $manager->driver('order_created');
            $manager->driver(UserDeactivatedType::IDENTIFIER);
            return $manager;
        });

        $this->app->singleton("manager/notification/types", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Notification\Type\Manager');
        });

        $this->app->singleton("manager/notification/subscriptions", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Notification\Type\Manager');
        });
    }

    private function overrideExporterCategoryManager()
    {
        $pkg = $this->packageService->getByHandle("migration_tool");
        if ($pkg instanceof PackageEntity) {
            $this->eventDispatcher->addListener("on_before_dispatch", function () {
                /** @noinspection PhpDeprecationInspection */
                $this->app->bindShared('migration/manager/exporters', function ($app) {
                    return new ExportManager($app);
                });
            });
        }
    }

    private function registerEventHandlers()
    {
        $this->eventDispatcher->addListener("on_user_login", function ($event) {
            /** @var \Concrete\Core\User\Event\User $event */

            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $categoryEntity = $service->getByHandle('customer');
            /** @var CustomerCategory $category */
            $category = $categoryEntity->getController();
            $setManager = $category->getSetManager();
            /** @var CustomerService $customerService */
            $customerService = $this->app->make(CustomerService::class);

            $userEntity = $event->getUserObject()->getUserInfoObject()->getEntityObject();

            if ($userEntity instanceof User) {
                $customer = $customerService->getByUserEntity($userEntity);

                if ($customer instanceof Customer) {
                    foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                        /** @var CustomerKey $ak */
                        $value = $customer->getAttribute($ak);
                        $this->cartService->setCustomerAttribute($ak->getAttributeKeyHandle(), $value);
                    }
                }
            }
        });
    }

    private function initializeSearchProviders()
    {
        $this->app->singleton("manager/search_field/tax_rate", function ($app) {
            return $app->make('Bitter\BitterShopSystem\TaxRate\Search\Field\Manager');
        });


        $this->app->singleton("manager/search_field/shipping_cost", function ($app) {
            return $app->make('Bitter\BitterShopSystem\ShippingCost\Search\Field\Manager');
        });

        $this->app->singleton("manager/search_field/product", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Product\Search\Field\Manager');
        });

        $this->app->singleton("manager/search_field/order", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Order\Search\Field\Manager');
        });

        $this->app->singleton("manager/search_field/customer", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Customer\Search\Field\Manager');
        });

        $this->app->singleton("manager/search_field/coupon", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Coupon\Search\Field\Manager');
        });

        $this->app->singleton("manager/search_field/category", function ($app) {
            return $app->make('Bitter\BitterShopSystem\Category\Search\Field\Manager');
        });
    }

    private function addImporterRoutines()
    {
        /** @var ImporterManager $importer */
        $importer = $this->app->make('import/item/manager');
        $importer->registerImporterRoutine($this->app->make(ImportTaxRatesRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportShippingCostsRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportCategoriesRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportProductsRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportCustomersRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportOrdersRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportCouponsRoutine::class));
        $importer->registerImporterRoutine($this->app->make(ImportPdfEditorRoutine::class));
    }

    private function initializeAutoloader()
    {
        /** @var PackageService $packageService */
        $packageService = $this->app->make(PackageService::class);
        /** @var Package|PackageEntity $pkg */
        $pkg = $packageService->getByHandle("bitter_shop_system");
        if ($pkg instanceof PackageEntity) {
            $autoloaderFile = $pkg->getPackagePath() . "/vendor/autoload.php";
            if (file_exists($autoloaderFile)) {
                /** @noinspection PhpIncludeInspection */
                require_once($autoloaderFile);
            }
        }
    }

    private function registerRoutes()
    {
        $this->router->loadRouteList(new RouteList());
    }

    private function overrideAttributeCategoryManager()
    {
        $this->app->singleton("manager/attribute/category", function ($app) {
            return new Manager($app);
        });
    }

    private function overridePackageItemCategoryManager()
    {
        $this->app->bind(CorePackageItemCategoryManager::class, PackageItemCategoryManager::class);
    }
}