/* eslint prefer-arrow-callback: 0 */

jQuery(document).ready(function () {
    jQuery('.service_content_dashboard_ordersinformations').on("click", ".service_orders__tr", function () {
        jQuery(this).find(".service_orders_openclose").toggleClass("opened");
        jQuery(this).closest(".service_orders__tr").find(".service_order_collapse").slideToggle();
    });

    jQuery(".asidelinks").on("click", function () {
        jQuery(this).toggleClass("opened");
    });

    jQuery(".custom_select").selectmenu();

    jQuery('.service_content_dashboard_ordersinformations').on("click", ".orderinfo_opener", function () {
        jQuery(".orderinfo").slideDown();
        jQuery('html, body').animate({scrollTop: jQuery('.orderinfo').offset().top - 100}, 1000);
    });



});
