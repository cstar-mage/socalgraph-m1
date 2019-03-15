/* eslint prefer-arrow-callback: 0 */

$(document).ready(function() {
    $(".service_orders__tr").on("click", function() {
        $(this).find(".service_orders_openclose").toggleClass("opened");
        $(this).closest(".service_orders__tr").find(".collapse").collapse('toggle');
    });

    $(".asidelinks").on("click", function() {
        $(this).toggleClass("opened");
    });



});