<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\BitterShopSystem\Entity\Attribute\Key\CustomerKey;
use Bitter\BitterShopSystem\Error\ErrorList\Formatter\BootstrapFormatter;
use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;

/** @var string $username */
/** @var string $password */
/** @var string $username */
/** @var string $checkoutMethod */
/** @var ErrorList|null $error */
/** @var string $isGuestCheckout */
/** @var string $email */
/** @var Renderer $renderer */
/** @var CustomerKey[] $attributes */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
$user = new User();
?>

<?php if (isset($error) && $error instanceof ErrorList && $error->has()) {
    $formatter = new BootstrapFormatter($error);
    echo $formatter->render();
} ?>

<div class="checkout step-personal-data">
    <h1>
        <?php echo t("Details"); ?>
    </h1>

    <p>
        <?php echo t("Please enter your details in the form below."); ?>
    </p>

    <form action="#" method="post">
        <?php echo $token->output("submit_contact_information"); ?>

        <?php if (!$user->isRegistered()) { ?>
            <div class="form-group">
                <?php echo $form->label("email", t("E-Mail")); ?>
                <?php echo $form->email("email", $email); ?>
            </div>

            <?php if ($checkoutMethod === "register") { ?>
                <?php if (!$config->get('concrete.user.registration.email_registration')) { ?>
                    <div class="form-group">
                        <?php echo $form->label("username", t("Username")); ?>
                        <?php echo $form->text("username", $username); ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <?php echo $form->label("password", t("Password")); ?>
                    <?php echo $form->password("password", $password); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->label("passwordRepeat", t("Repeat Password")); ?>
                    <?php echo $form->password("passwordRepeat", $password); ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="form-group">
                <?php echo $form->label("email", t("E-Mail")); ?>
                <?php echo $form->email("email", $user->getUserInfoObject()->getUserEmail(), ["disabled" => "disabled"]); ?>
            </div>
        <?php } ?>

        <?php if (!empty($attributes)) {
            foreach ($attributes as $ak) {
                $renderer->buildView($ak)->render();
            }
        } ?>

        <div class="float-end">
            <?php if ($config->get("concrete.user.registration.enabled") && !$user->isRegistered()) { ?>
                <a href="<?php echo Url::to(Page::getCurrentPage()); ?>"
                   class="btn btn-secondary"
                   rel="nofollow">
                    <i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo t("Back"); ?>
                </a>
            <?php } ?>

            <button type="submit" class="btn btn-primary">
                <?php echo t("Next"); ?>

                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>