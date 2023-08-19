<?php

/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

/** @noinspection DuplicatedCode */

use Bitter\BitterShopSystem\Coupon\Search\Result\Result;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var Result|null $result */




?>

<?php if (!is_object($result)): ?>
    <div class="alert alert-warning">
        <?php echo t('Currently there are no items available.'); ?>
    </div>
<?php else: ?>
    <script type="text/template" data-template="search-results-table-body">
        <% _.each(items, function (item) {%>
        <tr data-launch-search-menu="<%=item.id%>">
            <td class="ccm-search-results-icon">
                <%=item.resultsThumbnailImg%>
            </td>
            <% for (i = 0; i < item.columns.length; i++) {
            var column = item.columns[i]; %>
            <% if (i == 0) { %>
            <td class="ccm-search-results-name"><%-column.value%></td>
            <% } else { %>
            <td><%-column.value%></td>
            <% } %>
            <% } %>
        </tr>
        <% }); %>
    </script>

    <div data-search-element="wrapper"></div>

    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table ccm-search-results-table-icon">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="ccm-search-results-pagination"></div>
    </div>

    <script type="text/template" data-template="search-results-pagination">
        <%=paginationTemplate%>
    </script>
    <script type="text/template" data-template="search-results-menu">
        <div class="popover fade" data-search-menu="<%=item.id%>">
            <div class="arrow"></div>
            <div class="popover-inner">
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/coupons/edit"); ?>/<%=item.id%>">
                            <?php echo t("Edit"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo Url::to("/dashboard/bitter_shop_system/coupons/remove"); ?>/<%=item.id%>">
                            <?php echo t("Remove"); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </script>


    <script type="text/template" data-template="search-results-table-head">
        <tr>
            <th>
                <div class="dropdown">
                    <button class="btn btn-menu-launcher" disabled data-toggle="dropdown"><i
                            class="fa fa-chevron-down"></i></button>
                </div>
            </th>
            <%
            for (i = 0; i < columns.length; i++) {
            var column = columns[i];
            if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
            <% } else { %>
            <th><span><%-column.title%></span></th>
            <% } %>
            <% } %>
        </tr>
    </script>

    <script type="text/javascript">
        $(function () {
            $('#ccm-dashboard-content').concreteAjaxSearch(<?php echo json_encode(["result" => $result->getJSONObject()]) ?>);
        });
    </script>
<?php endif; ?>

<?php
