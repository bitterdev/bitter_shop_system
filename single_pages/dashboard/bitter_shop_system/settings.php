<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\PaymentProvider\PaymentConfigurationInterface;
use Bitter\BitterShopSystem\PaymentProvider\PaymentProviderInterface;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var bool $displayPricesIncludingTax */
/** @var PaymentProviderInterface[] $paymentProviders */
/** @var array $currencySymbolPositions */
/** @var string $notificationMailAddress */
/** @var string $currencySymbol */
/** @var string $currencyCode */
/** @var string $currencySymbolPosition */
/** @var int $currencySymbolSpaces */
/** @var int $decimals */
/** @var string $decimalPoint */
/** @var string $thousandsSeparator */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);

echo $userInterface->tabs([
    ['general', t("General"), true],
    ['payment-methods', t("Payment Providers")]
]);

?>

<form action="#" method="post">
    <div class="tab-content">
        <div class="tab-pane active" id="general" role="tabpanel">
            <?php echo $token->output("update_settings"); ?>

            <fieldset>
                <legend>
                    <?php echo t("General"); ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label("notificationMailAddress", t("Notification Mail Address")); ?>
                    <?php echo $form->email("notificationMailAddress", $notificationMailAddress); ?>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <?php echo $form->checkbox("displayPricesIncludingTax",1, $displayPricesIncludingTax); ?>
                        <?php echo $form->label('displayPricesIncludingTax', t("Display prices including tax"), ['class'=>'form-check-label']) ?>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <?php echo t("Money Format"); ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label("currencySymbol", t("Currency Symbol")); ?>
                    <?php echo $form->text("currencySymbol", $currencySymbol); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("currencyCode", t("Currency Code")); ?>
                    <?php echo $form->text("currencyCode", $currencyCode); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("currencySymbolPosition", t("Currency Symbol Position")); ?>
                    <?php echo $form->select("currencySymbolPosition", $currencySymbolPositions, $currencySymbolPosition); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("currencySymbolSpaces", t("Currency Symbol Space Counter")); ?>
                    <?php echo $form->number("currencySymbolSpaces", $currencySymbolSpaces); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("decimals", t("Decimals")); ?>
                    <?php echo $form->number("decimals", $decimals); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("decimalPoint", t("Decimal Point")); ?>
                    <?php echo $form->text("decimalPoint", $decimalPoint); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("thousandsSeparator", t("Thousands Separator")); ?>
                    <?php echo $form->text("thousandsSeparator", $thousandsSeparator); ?>
                </div>
            </fieldset>
        </div>

        <div class="tab-pane" id="payment-methods" role="tabpanel">
            <?php foreach ($paymentProviders as $paymentProvider) { ?>
                <?php if ($paymentProvider->getConfigurationElement() instanceof PaymentConfigurationInterface) { ?>
                    <fieldset>
                        <legend>
                            <?php echo $paymentProvider->getName() ?>
                        </legend>

                        <?php echo $paymentProvider->getConfigurationElement()->render(); ?>
                    </fieldset>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $form->submit('save', t('Save'), ['class' => 'btn btn-primary float-end']); ?>
        </div>
    </div>
</form>

<?php
