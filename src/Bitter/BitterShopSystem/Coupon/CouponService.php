<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Coupon;

use Bitter\BitterShopSystem\Checkout\CheckoutService;
use Bitter\BitterShopSystem\Entity\Coupon;
use Concrete\Core\Entity\Package;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class CouponService
{
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return object|Coupon|null
     */
    public function getById(
        int $id
    ): ?Coupon
    {
        return $this->entityManager->getRepository(Coupon::class)->findOneBy(["id" => $id]);
    }

    /**
     * @param string $code
     * @return object|Coupon|null
     */
    public function getByCode(
        string $code
    ): ?Coupon
    {
        return $this->entityManager->getRepository(Coupon::class)->findOneBy(["code" => $code]);
    }

    /**
     * @return Coupon[]
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Coupon::class)->findAll();
    }

    /**
     * @param Package $pkg
     * @return object[]
     */
    public function getListByPackage(
        Package $pkg
    ): array
    {
        return $this->entityManager->getRepository(Coupon::class)->findBy(["package" => $pkg]);
    }

    /**
     * @param string $code
     * @return ErrorList
     */
    public function redeemCouponByCode(
        string $code
    ): ErrorList
    {
        $coupon = $this->getByCode($code);

        if ($coupon instanceof Coupon) {
            return $this->redeemCoupon($coupon);
        } else {
            $errorList = new ErrorList();
            $errorList->add(t("Invalid coupon code."));
            return $errorList;
        }
    }

    /**
     * @param Coupon $coupon
     * @return ErrorList
     */
    public function redeemCoupon(
        Coupon $coupon
    ): ErrorList
    {
        $errorList = $coupon->validate();

        if (!$errorList->has()) {
            $app = Application::getFacadeApplication();
            /** @var CheckoutService $cartService */
            $cartService = $app->make(CheckoutService::class);
            $cartService->setCoupon($coupon);
        }

        return $errorList;
    }
}