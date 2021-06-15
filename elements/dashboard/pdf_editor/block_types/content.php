<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Bitter\BitterShopSystem\Entity\PdfEditor\Block;

$app = Application::getFacadeApplication();
/** @var Block $block */
/** @var Form $form */
$form = $app->make(Form::class);

?>

<div class="form-group">
    <?php echo $form->label("content", t("Content")); ?>
    <?php echo $form->textarea("content", $block->getContent()); ?>
</div>