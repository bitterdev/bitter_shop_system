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

        $("#variant").change(function (e) {
            e.preventDefault();
            e.stopPropagation();

            window.location.href = $(this).data("baseUrl") + "/" + $(this).find(":selected").val();

            return false;
        });
    });
})(jQuery);