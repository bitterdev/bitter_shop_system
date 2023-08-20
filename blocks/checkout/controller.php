<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\BitterShopSystem\Block\Checkout;

use Bitter\BitterShopSystem\Coupon\CouponService;
use Bitter\BitterShopSystem\Entity\Coupon;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Page\Page;
use Concrete\Package\BitterShopSystem\Controller\Element\Checkout\Complete;
use Concrete\Package\BitterShopSystem\Controller\Element\Checkout\Review;
use Concrete\Package\BitterShopSystem\Controller\Element\Checkout\ContactInformation;
use Concrete\Package\BitterShopSystem\Controller\Element\Checkout\CheckoutMethod;
use Concrete\Package\BitterShopSystem\Controller\Element\Checkout\PaymentMethod;

class Controller extends BlockController
{
    protected $btTable = "btCheckout";
    protected $btExportPageColumns = ['termsOfUsePageId', 'privacyPolicyPageId'];

    public function getBlockTypeDescription(): string
    {
        return t('Add a multi step checkout.');
    }

    public function getBlockTypeName(): string
    {
        return t('Checkout');
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset("css", "bootstrap");
        $this->requireAsset("css", "fontawesome");
    }

    public function action_redeem_coupon()
    {
        /** @var CouponService $couponService */
        $couponService = $this->app->make(CouponService::class);
        $errorList = $couponService->redeemCouponByCode((string)$this->request->query->get("couponCode"));
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Review"));
        /** @var Review $element */
        $element = $this->app->make(Review::class);
        $element->setTermsOfUsePage(Page::getByID($this->get("termsOfUsePageId")));
        $element->setPrivacyPolicyPage(Page::getByID($this->get("privacyPolicyPageId")));
        $element->setDisplayCaptcha((bool)$this->get("displayCaptcha"));
        $element->set("error", $errorList);
        $this->set("element", $element);
    }

    public function action_review()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Review"));
        /** @var Review $element */
        $element = $this->app->make(Review::class);
        $element->setTermsOfUsePage(Page::getByID($this->get("termsOfUsePageId")));
        $element->setPrivacyPolicyPage(Page::getByID($this->get("privacyPolicyPageId")));
        $element->setDisplayCaptcha((bool)$this->get("displayCaptcha"));
        $this->set("element", $element);
    }

    public function action_complete()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Thank You"));
        $this->set("element", $this->app->make(Complete::class));
    }

    public function action_payment_failed()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Payment"));
        $errorList = new ErrorList();
        $errorList->add(t("The selected payment method can not be used."));
        /** @var CheckoutMethod $element */
        $element = $this->app->make(PaymentMethod::class);
        $element->set("error", $errorList);
        $this->set("element", $element);
    }

    public function action_contact_information()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Contact Information"));
        $this->set("element", $this->app->make(ContactInformation::class));
    }

    public function action_payment_method()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Payment"));
        $this->set("element", $this->app->make(PaymentMethod::class));
    }

    public function save($args)
    {
        $args["displayCaptcha"] = isset($args["displayCaptcha"]) ? 1 : 0;
        parent::save($args);
    }

    public function add() {
        $this->set("displayCaptcha", false);
        $this->set("termsOfUsePageId", null);
        $this->set("privacyPolicyPageId", null);
    }

    public function view()
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment(t("Checkout Method"));
        $this->set("element", $this->app->make(CheckoutMethod::class));
    }
}
