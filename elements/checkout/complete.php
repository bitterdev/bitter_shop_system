<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Page\Stack\Stack;

?>

<div class="checkout step-complete">
    <?php
    $stack = Stack::getByName("Order Complete");
    $stack->display();
    ?>
</div>