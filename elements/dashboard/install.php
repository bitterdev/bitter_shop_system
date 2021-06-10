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

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<div class="checkbox">
    <label>
        <?php echo $form->checkbox("installSampleContent", 1, true); ?>

        <?php echo t("Install Sample Content"); ?>
    </label>
</div>

<div class="checkbox">
    <label>
        <?php echo $form->checkbox("enablePublicRegistration", 1, true); ?>

        <?php echo t("Enable Public Registration"); ?>
    </label>
</div>


