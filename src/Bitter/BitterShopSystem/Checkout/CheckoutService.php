<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\BitterShopSystem\Checkout;

use Bitter\BitterShopSystem\Attribute\Category\CustomerCategory;
use Bitter\BitterShopSystem\Coupon\CouponService as CouponService;
use Bitter\BitterShopSystem\Customer\CustomerService as CustomerService;
use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Entity\Attribute\Value\CustomerValue;
use Bitter\BitterShopSystem\Entity\Coupon;
use Bitter\BitterShopSystem\Entity\Customer;
use Bitter\BitterShopSystem\Entity\Order;
use Bitter\BitterShopSystem\Entity\OrderPosition;
use Bitter\BitterShopSystem\Entity\Product;
use Bitter\BitterShopSystem\Entity\ShippingCost;
use Bitter\BitterShopSystem\Entity\TaxRate;
use Bitter\BitterShopSystem\Events\CartItemAdded;
use Bitter\BitterShopSystem\Events\CartItemRemoved;
use Bitter\BitterShopSystem\Events\CartItemUpdated;
use Bitter\BitterShopSystem\Events\CustomerCreated;
use Bitter\BitterShopSystem\Events\CustomerUpdated;
use Bitter\BitterShopSystem\Events\OrderCreated;
use Bitter\BitterShopSystem\Notification\Type\OrderCreatedType;
use Bitter\BitterShopSystem\Order\OrderService;
use Bitter\BitterShopSystem\OrderConfirmation\OrderConfirmationService;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderService;
use Bitter\BitterShopSystem\Product\ProductService;
use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Mail\Service;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Notification\Type\Manager;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\IpAccessControlService;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\RegistrationService;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Utility\Service\Text;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Exception;

class CheckoutService implements ObjectInterface
{
    protected $app;
    /** @var Session */
    protected $session;
    protected $paymentProviderService;
    protected $productService;
    protected $eventDispatcher;
    protected $textService;
    protected $couponService;

    public function __construct(
        Application $app,
        ProductService $productService,
        PaymentProviderService $paymentProviderService,
        EventDispatcherInterface $eventDispatcher,
        Text $textService,
        CouponService $couponService
    )
    {
        $this->app = $app;
        $this->productService = $productService;
        $this->session = $this->app->make("session");
        $this->paymentProviderService = $paymentProviderService;
        $this->eventDispatcher = $eventDispatcher;
        $this->textService = $textService;
        $this->couponService = $couponService;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @throws Exception
     */
    public function addItem(Product $product, int $quantity = 1)
    {
        if ($quantity > $product->getQuantity()) {
            throw new Exception(t("You can't add more pieces of this product then available."));
        }

        $cartItems = $this->session->get("cartItems", []);

        $itemUpdated = false;

        foreach ($cartItems as $index => $cartItemRaw) {
            $cartItem = json_decode($cartItemRaw, true);

            if ($cartItem["product"]["handle"] === $product->getHandle()) {
                $cartItem["product"]["quantity"] += $quantity;
                $cartItems[$index] = json_encode($cartItem);
                $itemUpdated = true;
                $event = new CartItemUpdated();
                $event->setProduct($product);
                $event->setQuantity((int)$cartItem["product"]["quantity"]);
                $this->eventDispatcher->dispatch("cart_item_updated", $event);
                break;
            }
        }

        if (!$itemUpdated) {
            $cartItems[] = json_encode(new CheckoutItem($product, $quantity));
            $event = new CartItemAdded();
            $event->setProduct($product);
            $event->setQuantity($quantity);
            $this->eventDispatcher->dispatch("cart_item_added", $event);

        }

        $this->session->set("cartItems", $cartItems);
        $this->session->save();
    }

    public function removeItem(Product $product): bool
    {
        $cartItems = $this->session->get("cartItems", []);

        foreach ($cartItems as $index => $cartItemRaw) {
            $cartItem = json_decode($cartItemRaw, true);

            if ($cartItem["product"]["handle"] === $product->getHandle()) {
                unset($cartItems[$index]);
                $this->session->set("cartItems", $cartItems);
                $this->session->save();
                $event = new CartItemRemoved();
                $event->setProduct($product);
                $this->eventDispatcher->dispatch("cart_item_removed", $event);
                return true;
            }
        }

        return false;
    }

    /**
     * @param Product $product
     * @param int $newQuantity
     * @return bool
     * @throws Exception
     */
    public function updateItem(Product $product, int $newQuantity = 1): bool
    {
        if ($newQuantity > $product->getQuantity()) {
            throw new Exception(t("You can't add more pieces of this product then available."));
        }

        $cartItems = $this->session->get("cartItems", []);

        foreach ($cartItems as $index => $cartItemRaw) {
            $cartItem = json_decode($cartItemRaw, true);

            if ($cartItem["product"]["handle"] === $product->getHandle()) {
                $cartItem["quantity"] = $newQuantity;
                $cartItems[$index] = json_encode($cartItem);
                $this->session->set("cartItems", $cartItems);
                $this->session->save();
                $event = new CartItemUpdated();
                $event->setProduct($product);
                $event->setQuantity($newQuantity);
                $this->eventDispatcher->dispatch("cart_item_updated", $event);
                return true;
            }
        }

        return false;
    }

    /**
     * @return array|CheckoutItem[]
     */
    public function getAllItems(): array
    {
        $allItems = [];

        foreach ($this->session->get("cartItems", []) as $cartItemRaw) {
            $cartItem = json_decode($cartItemRaw, true);

            if ($this->getCheckoutPage() instanceof Page) {
                // if a checkout page is stored in the session use this page to prevent issues with epayment providers.
                $product = $this->productService->getByHandleWithLocale($cartItem["product"]["handle"], Section::getBySectionOfSite($this->getCheckoutPage())->getLocale());
            } else {
                $product = $this->productService->getByHandleWithCurrentLocale($cartItem["product"]["handle"]);
            }

            $quantity = (int)$cartItem["quantity"];

            if ($product instanceof Product) {
                $allItems[] = new CheckoutItem($product, $quantity);
            }
        }

        return $allItems;
    }

    public function getHighestShippingCost($includeTax = false): float
    {
        $highestShippingCostEntry = $this->getHighestShippingCostEntry();

        if ($highestShippingCostEntry instanceof ShippingCost) {
            if (count($highestShippingCostEntry->getVariants()) > 0) {
                $shippingAddress = $this->getCustomerAttribute("shipping_address");

                if ($shippingAddress instanceof AddressValue) {
                    $customerCountry = (string)$shippingAddress->getCountry();
                    $customerState = (string)$shippingAddress->getStateProvince();

                    foreach ($highestShippingCostEntry->getVariants() as $shippingCostVariant) {
                        if ($customerCountry === $shippingCostVariant->getCountry() &&
                            ($shippingCostVariant->getState() === "" || $customerState === $shippingCostVariant->getState())) {

                            return $shippingCostVariant->getPrice($includeTax);
                        }
                    }
                }
            }

            return $highestShippingCostEntry->getPrice($includeTax);
        }

        return 0;
    }

    public function getHighestShippingCostEntry(): ?ShippingCost
    {
        $highestShippingCostEntry = null;

        foreach ($this->getAllItems() as $cartItem) {
            if ($cartItem->getProduct() instanceof Product) {
                if ($cartItem->getProduct()->getShippingCost() instanceof ShippingCost) {
                    $shippingCostEntry = $cartItem->getProduct()->getShippingCost();

                    if ($highestShippingCostEntry === null ||
                        $shippingCostEntry->getPrice() > $highestShippingCostEntry->getPrice()) {
                        $highestShippingCostEntry = $shippingCostEntry;
                    }
                }
            }
        }

        return $highestShippingCostEntry;
    }

    public function completeOrder()
    {
        $this->session->remove("cartItems");
        $this->session->remove("couponCode");
        $this->session->remove("temporaryOrderId");
        $this->session->save();
    }

    public function getDiscount(bool $includeTax = false): float
    {
        $discount = 0;
        $coupon = $this->getCoupon();

        if ($coupon instanceof Coupon) {
            $totalToConsider = 0;

            foreach ($this->getAllItems() as $cartItem) {
                if ((
                        $coupon->isExcludeDiscountedProducts() &&
                        $cartItem->getProduct() instanceof Product &&
                        $cartItem->getProduct()->getPriceDiscounted() == 0
                    ) ||
                    !$coupon->isExcludeDiscountedProducts()
                ) {
                    $totalToConsider += $cartItem->getSubtotal();
                }
            }

            if ($coupon->isUsePercentageDiscount()) {
                $discount = $totalToConsider / 100 * $coupon->getDiscountPercentage();
            } else {
                $discount = $coupon->getDiscountPrice();
            }

            if ($coupon->getMaximumDiscountAmount() > 0 && $discount > $coupon->getMaximumDiscountAmount()) {
                $discount = $coupon->getMaximumDiscountAmount();
            }
        }

        if ($includeTax) {
            $discount += $discount / 100 * $coupon->getTaxRate()->getRate(true);
        }

        return $discount;
    }

    public function getTotal($incShippingCost = true, $incDiscounts = true): float
    {
        return $this->getSubtotal($incShippingCost, $incDiscounts) + $this->getTax($incShippingCost, $incDiscounts);
    }

    public function getSubtotal($incShippingCost = true, $incDiscounts = true): float
    {
        $subtotal = 0;

        foreach ($this->getAllItems() as $item) {
            $subtotal += $item->getSubtotal();
        }

        if ($incShippingCost) {
            $subtotal += $this->getHighestShippingCost();
        }

        if ($incDiscounts) {
            $subtotal -= $this->getDiscount();
        }

        return $subtotal;
    }

    public function getTax($incShippingCost = true, $incDiscounts = true): float
    {
        $tax = 0;

        foreach ($this->getAllItems() as $item) {
            $tax += $item->getTax();
        }

        if ($incDiscounts &&
            $this->getCoupon() instanceof Coupon &&
            $this->getCoupon()->getTaxRate() instanceof TaxRate) {
            $tax -= $this->getDiscount() / 100 * $this->getCoupon()->getTaxRate()->getRate(true);
        }

        if ($incShippingCost &&
            $this->getHighestShippingCostEntry() instanceof ShippingCost &&
            $this->getHighestShippingCostEntry()->getTaxRate() instanceof TaxRate) {
            $tax += $this->getHighestShippingCost() / 100 * $this->getHighestShippingCostEntry()->getTaxRate()->getRate(true);
        }

        return $tax;
    }

    public function getSelectedPaymentProvider(): ?PaymentProviderInterface
    {
        if ($this->session->has("selectedPaymentMethodHandle")) {
            return $this->paymentProviderService->getByHandle($this->session->get("selectedPaymentMethodHandle"));
        } else {
            $availablePaymentProviders = $this->paymentProviderService->getAvailablePaymentProviders();
            $availablePaymentProviders = array_reverse($availablePaymentProviders);
            return array_pop($availablePaymentProviders);
        }
    }

    public function setSelectedPaymentProvider(PaymentProviderInterface $paymentMethod): void
    {
        $this->session->set("selectedPaymentMethodHandle", $paymentMethod->getHandle());
        $this->session->save();
    }

    public function hasPaymentProviderSelect(): bool
    {
        return $this->session->has("selectedPaymentMethodHandle");
    }

    public function getSelectedCheckoutMethod(): string
    {
        return $this->session->get("selectedCheckoutMethod", "login");
    }

    public function setSelectedCheckoutMethod(string $selectedCheckoutMethod)
    {
        $this->session->set("selectedCheckoutMethod", $selectedCheckoutMethod);
        $this->session->save();
    }

    public function setCustomerUsername(string $password)
    {
        $this->session->set("customerUsername", $password);
        $this->session->save();
    }

    public function setCheckoutPageId(int $checkoutPageId)
    {
        $this->session->set("checkoutPageId", $checkoutPageId);
        $this->session->save();
    }

    public function getCheckoutPage(): ?Page
    {
        return Page::getByID($this->getCheckoutPageId());
    }

    public function getCheckoutPageId(): ?int
    {
        return $this->session->get("checkoutPageId");
    }

    public function getCustomerUsername(): ?string
    {
        return $this->session->get("customerUsername");
    }

    public function setCoupon(Coupon $coupon)
    {
        $this->session->set("couponCode", $coupon->getCode());
        $this->session->save();
    }

    public function getCoupon(): ?Coupon
    {
        $coupon = $this->couponService->getByCode((string)$this->session->get("couponCode"));
        if ($coupon instanceof Coupon && !$coupon->validate()->has()) {
            return $coupon;
        } else {
            return null;
        }
    }

    public function setCustomerPassword(string $password)
    {
        $this->session->set("customerPassword", $password);
        $this->session->save();
    }

    public function getCustomerPassword(): ?string
    {
        return $this->session->get("customerPassword");
    }

    public function getCustomerMailAddress(): ?string
    {
        $user = new User();
        if ($user->isRegistered()) {
            return $user->getUserInfoObject()->getUserEmail();
        } else {
            return $this->session->get("customerMailAddress");
        }
    }

    public function setCustomerMailAddress(string $customerMailAddress): void
    {
        $this->session->set("customerMailAddress", $customerMailAddress);
        $this->session->save();
    }

    public function getCustomerAttribute(string $akHandle)
    {
        return unserialize($this->session->get("customerAttribute." . lcfirst($this->textService->camelcase($akHandle))));
    }

    public function setCustomerAttribute(string $akHandle, $akValue): void
    {
        $this->session->set("customerAttribute." . lcfirst($this->textService->camelcase($akHandle)), serialize($akValue));
        $this->session->save();
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        $attributeValueObject = new CustomerValue();
        $attributeValueObject->setAttributeKey($ak);
        $attributeValueObject->setAttributeValueObject($this->getCustomerAttribute($ak->getAttributeKeyHandle()));
        return $attributeValueObject;
    }

    public function getAttributeValue($ak)
    {
        return null;
    }

    public function getObjectAttributeCategory()
    {
        return null;
    }

    public function clearAttribute($ak)
    {
        return null;
    }

    public function setAttribute($ak, $value)
    {
        return null;
    }

    public function getAttribute($ak, $mode = false)
    {
        return null;
    }

    public function transformToRealOrder(): Order
    {
        $errorList = new ErrorList();
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->app->make(EventDispatcher::class);
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        /** @var Service $mailService */
        $mailService = $this->app->make(Service::class);
        /** @var LoginService $loginService */
        $loginService = $this->app->make(LoginService::class);
        /** @var IpAccessControlService $ipAccessControlService */
        $ipAccessControlService = $this->app->make(IpAccessControlService::class);
        /** @var CustomerService $customerService */
        $customerService = $this->app->make(CustomerService::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var CategoryService $service */
        $service = $this->app->make(CategoryService::class);
        /** @var RegistrationService $registrationService */
        $registrationService = $this->app->make(RegistrationService::class);
        /** @var OrderService $orderService */
        $orderService = $this->app->make(OrderService::class);
        /** @var OrderConfirmationService $orderConfirmationService */
        $orderConfirmationService = $this->app->make(OrderConfirmationService::class);
        $categoryEntity = $service->getByHandle('customer');
        /** @var CustomerCategory $category */
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        if ($this->session->has("temporaryOrderId")) {
            $order = $orderService->getById((int)$this->session->get("temporaryOrderId"));
            $entityManager->remove($order);
            $entityManager->flush();

            $customer = $customerService->getById((int)$this->session->get("customerId"));
        } else {

            if ($this->getSelectedCheckoutMethod() === "register") {
                $userInfo = $registrationService->create([
                    "uIsValidated" => 1,
                    "uIsFullRecord" => 1,
                    "uName" => $config->get('concrete.user.registration.email_registration') ? null : $this->getCustomerUsername(),
                    "uPassword" => $this->getCustomerPassword(),
                    "uEmail" => $this->getCustomerMailAddress()
                ]);

                if (!$userInfo instanceof UserInfo) {
                    $errorList->add(t("There was en error while creating the user account."));
                } else {
                    if ($ipAccessControlService->isBlacklisted()) {
                        $errorList->add($ipAccessControlService->getErrorMessage());
                    } else {
                        $loginService->loginByUserID($userInfo->getUserID());
                        $this->setSelectedCheckoutMethod("login");
                    }
                }
            }

            $user = new User();
            $userEntity = $user->isRegistered() ? $user->getUserInfoObject()->getEntityObject() : null;

            if ($this->getSelectedCheckoutMethod() === "login") {
                $customer = $customerService->getByUserEntity($userEntity);

                if (!$customer instanceof Customer) {
                    $customer = new Customer();
                    $customer->setUser($userEntity);
                    $customer->setEmail($userEntity->getUserEmail());
                }
            } else {
                $customer = $customerService->getByMailAddress($this->getCustomerMailAddress());

                if (!$customer instanceof Customer) {
                    $customer = new Customer();
                    $customer->setEmail($this->getCustomerMailAddress());
                }
            }

            if ($customer->getId() === null) {
                $customerCreatedEvent = new CustomerCreated();
                $customerCreatedEvent->setCustomer($customer);
                $eventDispatcher->dispatch("on_customer_created", $customerCreatedEvent);
            } else {
                $customerUpdatedEvent = new CustomerUpdated();
                $customerUpdatedEvent->setCustomer($customer);
                $eventDispatcher->dispatch("on_customer_updated", $customerUpdatedEvent);
            }

            if ($userEntity instanceof \Concrete\Core\Entity\User\User) {
                $customer->setUser($userEntity);
            }

            $entityManager->persist($customer);
            $entityManager->flush();

            foreach ($setManager->getUnassignedAttributeKeys() as $ak) {
                /** @var CustomerKey $ak */
                $customer->setAttribute($ak, $this->getCustomerAttribute($ak->getAttributeKeyHandle()));
            }
        }

        $order = new Order();

        $order->setOrderDate(new DateTime());
        $order->setPaymentProviderHandle($this->getSelectedPaymentProvider()->getHandle());
        $order->setSubtotal($this->getSubtotal());
        $order->setTax($this->getTax());
        $order->setTotal($this->getTotal());
        $order->setCustomer($customer);

        foreach ($this->getAllItems() as $cartItem) {
            $orderPosition = new OrderPosition();
            $orderPosition->setDescription($cartItem->getProduct()->getName());
            $orderPosition->setPrice($cartItem->getSubtotal());
            $orderPosition->setTax($cartItem->getTax());
            $orderPosition->setProduct($cartItem->getProduct());
            $orderPosition->setOrder($order);
            $orderPosition->setQuantity((int)$cartItem->getQuantity());
            $entityManager->persist($orderPosition);
        }

        if ($this->getHighestShippingCost() > 0) {
            $orderPosition = new OrderPosition();
            $orderPosition->setDescription(t("Shipping"));
            $orderPosition->setPrice($this->getHighestShippingCost());

            if ($this->getHighestShippingCostEntry() instanceof ShippingCost &&
                $this->getHighestShippingCostEntry()->getTaxRate() instanceof TaxRate) {
                $orderPosition->setTax($this->getHighestShippingCost() / 100 * $this->getHighestShippingCostEntry()->getTaxRate()->getRate(true));
            }

            $orderPosition->setQuantity(1);
            $orderPosition->setOrder($order);
            $entityManager->persist($orderPosition);
        }

        if ($this->getCoupon() instanceof Coupon && $this->getDiscount() > 0) {
            $orderPosition = new OrderPosition();
            $orderPosition->setDescription(t("Discount Code: %s", $this->getCoupon()->getCode()));
            $orderPosition->setPrice($this->getDiscount() * -1);

            if ($this->getCoupon()->getTaxRate() instanceof TaxRate) {
                $orderPosition->setTax($this->getDiscount() / 100 * $this->getCoupon()->getTaxRate()->getRate(true) * -1);
            }

            $orderPosition->setQuantity(1);
            $orderPosition->setOrder($order);
            $entityManager->persist($orderPosition);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        $orderCompleteEvent = new OrderCreated();
        $orderCompleteEvent->setOrder($order);
        $eventDispatcher->dispatch("on_order_created", $orderCompleteEvent);

        /** @var Manager $notificationManager */
        $notificationManager = $this->app->make(Manager::class);
        /** @var OrderCreatedType $notificationType */
        $notificationType = $notificationManager->driver('order_created');
        $notifier = $notificationType->getNotifier();
        $subscription = $notificationType->getSubscription($order);
        $notified = $notifier->getUsersToNotify($subscription, $order);
        $notification = $notificationType->createNotification($order);
        $notifier->notify($notified, $notification);

        foreach ($this->getAllItems() as $cartItem) {
            if ($cartItem->getProduct() instanceof Product) {
                $product = $cartItem->getProduct();
                $product->setQuantity($product->getQuantity() - $cartItem->getQuantity());
                $entityManager->persist($product);
            }
        }

        if ($this->getCoupon() instanceof Coupon) {
            $coupon = $this->getCoupon();
            if ($coupon->isLimitQuantity()) {
                $coupon->setQuantity($coupon->getQuantity() - 1);
                $entityManager->persist($coupon);
            }
        }

        $entityManager->flush();
        $entityManager->refresh($order);

        $mailService->addParameter("order", $order);
        $mailService->load("order_confirmation", "bitter_shop_system");
        $mailService->addRawAttachment($orderConfirmationService->createPdfOrderConfirmation($order)->Output("S"), t("Order Confirmation") . ".pdf", "application/pdf");
        $mailService->to($this->getCustomerMailAddress());

        if (filter_var($config->get("bitter_shop_system.notification_mail_address"), FILTER_VALIDATE_EMAIL)) {
            $mailService->bcc($config->get("bitter_shop_system.notification_mail_address"));
        }

        try {
            $mailService->sendMail();
        } catch (Exception $e) {
            $errorList->add(t("There was an error while sending the order confirmation mail."));
        }

        $this->session->set("temporaryOrderId", $order->getId());
        $this->session->set("customerId", $customer->getId());
        $this->session->save();

        return $order;
    }
}