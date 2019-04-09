/* eslint prefer-arrow-callback: 0 */

jQuery(document).ready(function () {
    jQuery('.service_content_dashboard_ordersinformations').on("click", ".service_orders__tr", function () {
        jQuery(this).find(".service_orders_openclose").toggleClass("opened");
        jQuery(this).closest(".service_orders__tr").find(".service_order_collapse").slideToggle();
    });

    jQuery(".asidelinks").on("click", function() {
        jQuery(this).toggleClass("opened");
    });

    jQuery(".custom_select").selectmenu();
    jQuery(".datepicker input").datepicker();

    jQuery('.service_content_dashboard_ordersinformations').on("click", ".orderinfo_opener", function () {
        jQuery(".orderinfo").slideDown();
        jQuery('html, body').animate({
            scrollTop: jQuery('.orderinfo').offset().top - 100
        }, 1000);
    });

    jQuery(".search_item_form_quanity .inc").on("click", function() {
        var curr_val = jQuery(this).parents(".search_item_form_quanity").find("input").val();
        var curr_cost = jQuery(this).closest(".search_item").find(".search_item_cost_curr").html();
        if (curr_val < 999) {
            ++curr_val;
            jQuery(this).parents(".search_item_form_quanity").find("input").val(curr_val);
        }
        var curr_total = (curr_val * curr_cost).toFixed(2);
        jQuery(this).closest(".search_item_form").find(".search_item_input_stock").val(curr_total);
    });

    jQuery(".search_item_form_quanity .dec").on("click", function() {
        var curr_val = jQuery(this).parents(".search_item_form_quanity").find("input").val();
        var curr_cost = jQuery(this).closest(".search_item").find(".search_item_cost_curr").html();
        if (curr_val > 1) {
            --curr_val;
            jQuery(this).parents(".search_item_form_quanity").find("input").val(curr_val);
        }
        var curr_total = (curr_val * curr_cost).toFixed(2);
        jQuery(this).closest(".search_item_form").find(".search_item_input_stock").val(curr_total);
    });

    jQuery(".receipt_opener").on("click", function() {
        jQuery(this).toggleClass("opened");
        jQuery(this).closest(".receipt").find(".receipt_content").slideToggle()
    });

    jQuery(".with_menu3").on("click", function() {
        jQuery(this).toggleClass("opened");
        jQuery(this).find(".main_menu_3").slideToggle()
    });

    jQuery(".header_left_menu").on("click", function() {
        jQuery(".main_menu").fadeToggle();
    });

    jQuery(".main_menu_1").on("click", function() {
        if (jQuery(this).hasClass("active")) {
            jQuery(this).removeClass("active");
            jQuery(this).closest(".main_menu_row_col").find(".main_menu_2").removeClass("opened");
        } else {
            jQuery(".main_menu_1").removeClass("active");
            jQuery(this).addClass("active");
            jQuery(".main_menu_2").removeClass("opened");
            jQuery(this).closest(".main_menu_row_col").find(".main_menu_2").addClass("opened");
        }
    });

    jQuery(".notification_popup_markall").on("click", function() {
        jQuery(this).fadeOut();
        jQuery(".notification_popup_item").addClass("readed");
    });

    jQuery(".header_notification_opener").on("click", function() {
        jQuery(".header_notification_popup_bg").show()
        jQuery(".header_notification_popup").fadeIn();
    });

    jQuery(".header_notification_popup_bg").on("click", function() {
        jQuery(".header_notification_popup, .header_notification_popup_bg").fadeOut();
    });



});

jQuery(window).on("load resize", function() {
    if (jQuery(window).width() < '1280') {

    }
});