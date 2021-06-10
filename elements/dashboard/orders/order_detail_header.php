<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\BitterShopSystem\Entity\Order;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var Order $entry */
?>

<?php if (!$entry->isPaymentReceived()) { ?>
    <a href="<?php echo Url::to(Page::getCurrentPage(), "mark_as_paid", $entry->getId()); ?>" class="btn btn-success">
        <?php echo t("Mark as paid"); ?>
    </a>
<?php } ?>
