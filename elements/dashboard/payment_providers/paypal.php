<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var array $availableModes */
/** @var string $selectedMode */
/** @var string $clientId */
/** @var string $clientSecret */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<div class="form-group">
    <?php echo $form->label("selectedMode", t("Mode")); ?>
    <?php echo $form->select("selectedMode", $availableModes, $selectedMode); ?>
</div>

<div class="form-group">
    <?php echo $form->label("clientId", t("Client Id")); ?>
    <?php echo $form->text("clientId", $clientId); ?>
</div>

<div class="form-group">
    <?php echo $form->label("clientSecret", t("Client Secret")); ?>
    <?php echo $form->text("clientSecret", $clientSecret); ?>
</div>
