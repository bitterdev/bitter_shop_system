<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\PaymentProvider;

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Order\OrderService;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Package\PackageService;
use Concrete\Package\BitterShopSystem\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentProvider
{
    protected $request;
    protected $loggerFactory;
    /** @var LoggerInterface */
    protected $logger;
    protected $responseFactory;
    protected $orderService;
    protected $entityManager;
    protected $app;
    protected $eventDispatcher;
    protected $config;
    protected $packageService;
    protected $checkoutService;
    /** @var Controller */
    protected $pkg;

    public function __construct(
        Request $request,
        LoggerFactory $loggerFactory,
        ResponseFactory $responseFactory,
        OrderService $orderService,
        EntityManagerInterface $entityManager,
        Application $app,
        EventDispatcherInterface $eventDispatcher,
        Repository $config,
        PackageService $packageService,
        CheckoutService $checkoutService
    )
    {
        $this->request = $request;
        $this->loggerFactory = $loggerFactory;
        $this->logger = $this->loggerFactory->createLogger("bitter_shop_system");
        $this->responseFactory = $responseFactory;
        $this->orderService = $orderService;
        $this->entityManager = $entityManager;
        $this->app = $app;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
        $this->packageService = $packageService;
        $this->pkg = $this->packageService->getByHandle("bitter_shop_system")->getController();
        $this->checkoutService = $checkoutService;
        $this->on_start();
    }

    public function on_start(): void
    {

    }
}