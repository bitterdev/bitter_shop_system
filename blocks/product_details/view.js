/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

(function ($) {
    $(function () {
        $(".ccm-page .detail-image").click(function () {
            $("#ccm-large-image").attr("src", $(this).data("largeImageUrl"));
        });
    });
})(jQuery);