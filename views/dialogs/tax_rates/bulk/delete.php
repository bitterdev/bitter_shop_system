<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\BitterShopSystem\Entity\TaxRate;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Number;

/** @var $controller */
/** @var TaxRate[] $taxRates */

$app = Application::getFacadeApplication();
/** @var Number $nh */
$nh = $app->make(Number::class);

?>

<?php if (!is_array($taxRates) || count($taxRates) == 0) { ?>
    <div class="alert-message info">
        <?php echo t("No tax rates are eligible for this operation"); ?>
    </div>
<?php } else { ?>
    <p>
        <?php echo t('Are you sure you would like to delete the following tax rates?'); ?>
    </p>

    <form method="post" data-dialog-form="bulk-remove-tax-rates" action="<?php echo $controller->action('submit'); ?>">
        <?php foreach ($taxRates as $taxRate) { ?>
            <input type="hidden" name="item[]" value="<?php echo $taxRate->getId(); ?>"/>
        <?php } ?>

        <div class="ccm-ui">
            <table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <th>
                        <?php echo t('Name') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($taxRates as $taxRate) { ?>
                    <tr>
                        <td>
                            <?php echo $taxRate->getName(); ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" data-dialog-action="cancel">
                <?php echo t('Cancel'); ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-primary ms-auto">
                <?php echo t('Delete'); ?>
            </button>
        </div>

    </form>
<?php } ?>
