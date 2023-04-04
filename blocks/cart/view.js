/**
 * @project:   Bitter Shop System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

(function ($) {
    $(function () {
        $(".quantity-selector").change(function () {
            $(this).closest("form").trigger("submit");
        });
    });
})(jQuery);
