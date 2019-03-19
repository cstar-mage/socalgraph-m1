/* eslint prefer-arrow-callback: 0 */

jQuery(document).ready(function() {
    jQuery(".service_orders__tr").on("click", function() {
        jQuery(this).find(".service_orders_openclose").toggleClass("opened");
        jQuery(this).closest(".service_orders__tr").find(".collapse").collapse('toggle');
    });

    jQuery(".asidelinks").on("click", function() {
        jQuery(this).toggleClass("opened");
    });



});