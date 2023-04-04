<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Attribute\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Identifier;

/** @var array|File[] $currentFiles */
/** @var Controller $view */
/** @var int $akMaxFilesCount */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);
/** @var Identifier $identifier */
$identifier = $app->make(Identifier::class);

$idPrefix = "ccm-" . $identifier->getString() . "-";
?>

<?php for ($i = 1; $i <= $akMaxFilesCount; $i++) { ?>
    <div class="form-group">
        <?php echo $form->label($idPrefix . $i, t("File")); ?>
        <?php echo $fileManager->file(
            $idPrefix . $i,
            $view->field('value') . "[" . $i . "]",
            t("Please select a file"),
            $currentFiles[$i - 1] ?? null
        ); ?>
    </div>
<?php } ?>
