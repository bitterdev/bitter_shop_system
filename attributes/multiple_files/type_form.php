<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Form\Service\Form;

/** @var int $akMaxFilesCount */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<fieldset>
    <legend>
        <?php echo t('Multiple Files Options') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("akMaxFilesCount", t("Maximum count of files")); ?>
        <?php echo $form->number("akMaxFilesCount", $akMaxFilesCount, ["min" => 1]); ?>
    </div>
</fieldset>