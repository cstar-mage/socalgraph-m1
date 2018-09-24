/**
 * JS for tender-greens theme.
 */



jQuery(document).ready(function () {


    //Show Search

    (function () {
        jQuery('#search-icon').on('click', function () {
            jQuery('#header-search').toggleClass('hide');
            jQuery("#search").focus();
        })
    })();

    // ENDShow Search
    if (jQuery(window).width() < 1201) {
        jQuery('#tg-menu').append(jQuery('#tg-login').addClass('displace'));
    }
    if (jQuery(window).width() > 1201) {
        jQuery('.tg-header-right-container').append(jQuery('#tg-login').removeClass('displace'));
    }
    if (jQuery(window).width() < 770) {
        jQuery('#tg-menu').prepend(jQuery('#header-search').addClass('mobile-show'));
    }
    if (jQuery(window).width() > 770) {
        jQuery('.tg-search').prepend(jQuery('#header-search').removeClass('mobile-show'));
    }
    jQuery( window ).resize(function() {
    if (jQuery(window).width() < 1201) {
        jQuery('#tg-menu').append(jQuery('#tg-login').addClass('displace'));
    }
    if (jQuery(window).width() > 1201) {
        jQuery('.tg-header-right-container').append(jQuery('#tg-login').removeClass('displace'));
    }
    if (jQuery(window).width() < 770) {
        jQuery('#tg-menu').prepend(jQuery('#header-search').addClass('mobile-show'));
    }
    if (jQuery(window).width() > 770) {
        jQuery('.tg-search').prepend(jQuery('#header-search').removeClass('mobile-show'));
    }

    });
    window.inputNumber = function (el) {

        var min = el.attr('min') || true;
        var max = el.attr('max') || false;

        var els = {};

        els.inc = el.next();
        els.dec = el.prev();

        el.each(function () {
            init(jQuery(this));
        });

        function init(el) {

            els.dec.on('click', decrement);
            els.inc.on('click', increment);

            function decrement() {
                var value = el[0].value;
                value--;
                if (!min || value >= min) {
                    el[0].value = value;
                }
            }

            function increment() {
                var value = el[0].value;
                value++;
                if (!max || value <= max) {
                    el[0].value = value++;
                }
            }
        }
    };

    jQuery('.item, .cart-item-checkout, .product-view').each(function () {
        inputNumber(jQuery(this).find('.input-number'));
    });

    jQuery(function () {
        function trackOrder(orderNum) {
            if (orderNum.length == 0) {
                jQuery("#find-order").parents().find('.order_number').addClass('err-red');
                setTimeout(function () {
                    jQuery("#find-order").parents().find('.order_number').removeClass('err-red');
                }, 1000);
                return;
            }

            jQuery.ajax({
                url: '/shipping/tracking/progress',
                data: {order_id: orderNum},
                success: function (response) {
                    if (response) {
                        jQuery('.shipping-status-container').html(response);
                        jQuery(".shipping-status").slideDown("slow");
                    } else {
                        jQuery("#find-order").parents().find('.order_number').addClass('err-red');
                        setTimeout(function () {
                            jQuery("#find-order").parents().find('.order_number').removeClass('err-red');
                        }, 1000);
                    }
                },
                error: function () {
                    jQuery("#find-order").parents().find('.order_number').addClass('err-red');
                    setTimeout(function () {
                        jQuery("#find-order").parents().find('.order_number').removeClass('err-red');
                    }, 1000);
                }
            });
        }

        jQuery('.track_order_container form.order_number').submit(function (event) {
           event.preventDefault();
           var orderNum = jQuery(this).find('input').val();
           trackOrder(orderNum);
        });

        jQuery("#find-order").click(function () {
            var orderNum = jQuery(this).parent().find('input').val();
            trackOrder(orderNum);
        });
    });

    jQuery("#nav1 li").hover(
        function () {
            jQuery(this).find('ul').fadeIn(100);
        },
        function () {
            jQuery(this).find('ul').fadeOut(50);
        });


    jQuery('#Notifications').click(function () {
        jQuery(this).parent().find('.tg-notifications').toggleClass('hide');
    });


    jQuery("#password").change(function () {
        jQuery('#confirmation').val(jQuery('#password').val());
    });
    var mhdescr = 0;
    jQuery(".checkout-cart-index .t-products-descr").each(function () {
        var h_block = parseInt(jQuery(this).height());
        if (h_block > mhdescr) {
            mhdescr = h_block;
        }
    });
    jQuery(".checkout-cart-index .t-products-descr").css('min-height', mhdescr);


    jQuery('.qty-wrapper .qty').change(function () {
        jQuery(this).parents().eq(2).find('.btn-update').removeClass('hide');
    });
    jQuery('.qty-wrapper span').click(function () {
        jQuery(this).parents().eq(2).find('.btn-update').removeClass('hide');
    });
    jQuery('.item-address-select').change(function () {
        jQuery(this).closest('.cart-item-checkout').find('.btn-update').removeClass('hide');
    });


//    totat-qty in cart
    var sum = 0;
    jQuery(".body-table-cart-item .qty-wrapper input").each(function () {
        sum += +(jQuery(this).val());  // sum += parseInt($(this).val());
    });
    jQuery('.table-footer .total-numb').prepend(sum);

    // fake-color-swatcher


    jQuery(".atrribute-color .color-red").click(function () {
        jQuery("#image-main").attr("src","http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-red.png");
        jQuery('.zoomWindowContainer > div').css("background-image", "url('http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-red.png')");
    });
    jQuery(".atrribute-color .color-yellow").click(function () {
        jQuery("#image-main").attr("src","http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-yellow.png");
        jQuery('.zoomWindowContainer > div').css("background-image", "url('http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-yellow.png')");
    });
    jQuery(".atrribute-color .color-blue").click(function () {
        jQuery("#image-main").attr("src","http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-blue.png");
        jQuery('.zoomWindowContainer > div').css("background-image", "url('http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder-blue.png')");
    });
    jQuery(".products-grid .item").each(function () {
        jQuery(this).find(".color-circle-red").click(function () {
            jQuery(this).parents().eq(1).find(".product-image img").attr("src", "http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder_1-red.png");
        });
        jQuery(this).find(".color-circle-blue").click(function () {
            jQuery(this).parents().eq(1).find(".product-image img").attr("src", "http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder_1-blue.png");
        });
        jQuery(this).find(".color-circle-yellow").click(function () {
            jQuery(this).parents().eq(1).find(".product-image img").attr("src", "http://tg.noxsterprojects.com/skin/frontend/tendergreens/default/images/placeholder_1-yellow.png");
        });
    });

    // close-button-popup

    jQuery('#close-notification').click(function () {
        jQuery(this).parents().eq(2).find('.tg-notifications').addClass('hide');
    });
    jQuery('#close-popup-header-cart').click(function () {
        jQuery(this).parents().eq(2).find('#header-cart').removeClass('skip-active');
    });
    jQuery('#RequestForm').on('shown.bs.modal', function () {
    });

    //more orders in account dashboard

    jQuery('#more-orders').click(function() {
        var next = jQuery('#ResultsPerPage option:selected').next();
        if (next.length) {
            jQuery('#ResultsPerPage').val(next.attr('value')).trigger('change');
        }
    });
    //slice cats
    var showNum = 4;
    jQuery('.category__item').slice(showNum, 99).addClass('hide');

    jQuery('.category__tg_button').click(function () {
        jQuery('.category__item.hide').slice(0, showNum).removeClass('hide');
        if (jQuery('.category__item.hide').length < 1) {
            jQuery(this).hide();
        }
    });
    if (jQuery('.category__item.hide').length < 1) {
        jQuery('.category__tg_button').hide();
    }
    if (jQuery('.category__item.hide').length >= 1) {
        jQuery(".category__tg_button").removeClass("hide");
    }
    jQuery('#tg-login').click(function () {
        jQuery(this).find('.dropdown-menu-acc').toggleClass('hide').toggleClass('dropdown-menu-acc-open');
    });
    jQuery(function($){
        $(document).mouseup(function (e){ // событие клика по веб-документу
            var login = $("#tg-login"); // тут указываем ID элемента
            if (!login.is(e.target) // если клик был не по нашему блоку
                && login.has(e.target).length === 0) { // и не по его дочерним элементам
                jQuery(this).find('.dropdown-menu-acc').addClass('hide'); // скрываем его
            }
            var cart = $(".header-minicart, .remove-item-from-cart"); // тут указываем ID элемента
            if (!cart.is(e.target) // если клик был не по нашему блоку
                && cart.has(e.target).length === 0) { // и не по его дочерним элементам
                jQuery("#header-cart").removeClass('skip-active'); // скрываем его
            }
            var notifications = $(".Notifications"); // тут указываем ID элемента
            if (!notifications.is(e.target) // если клик был не по нашему блоку
                && notifications.has(e.target).length === 0) { // и не по его дочерним элементам
                jQuery(".tg-notifications").addClass('hide'); // скрываем его
            }

            var menu = $(".tg-menu-active"); // тут указываем ID элемента
            var hamburger = $(".hamburger-menu"); // тут указываем ID элемента
            if (!menu.is(e.target) // если клик был не по нашему блоку
                &&!hamburger.is(e.target)
                && hamburger.has(e.target).length === 0
                    && menu.has(e.target).length === 0) { // и не по его дочерним элементам
                menu.addClass('hide'); // скрываем его
                jQuery('.hamburger-menu .bar').removeClass('animate');
            }
            var removePopup = $(".remove-item-from-cart"); // тут указываем ID элемента
            if (!removePopup.is(e.target) // если клик был не по нашему блоку
                && removePopup.has(e.target).length === 0) { // и не по его дочерним элементам
                jQuery(removePopup).hide(); // скрываем его
            }
        });
    });


    // Hamburger-menu in Header

    (function () {
        jQuery('.hamburger-menu').on('click', function () {
            jQuery('.bar').toggleClass('animate');
            jQuery('#tg-menu').toggleClass('hide');
        });
    })();

    // END Hamburger-menu in Header
});


window.onload = function () {
    jQuery('.5days .donut').viewportChecker({
        classToAdd: 'donut--25', // Class to add to the elements when they are visible
        offset: 100
    });
    jQuery('.14days .donut').viewportChecker({
        classToAdd: 'donut--50', // Class to add to the elements when they are visible
        offset: 100
    });

    jQuery( window ).resize(function() {
        var mh = 0;
        jQuery(".printing-category-grid-container .col-6 img").each(function () {
            var h_block = parseInt(jQuery(this).height());
            if (h_block > mh) {
                mh = h_block;
            }
        });
        jQuery(".printing-category-grid-container .col-6").css('height', mh);
    });
    
    var mh = 0;
    jQuery(".printing-category-grid-container .col-6 img").each(function () {
        var h_block = parseInt(jQuery(this).height());
        if (h_block > mh) {
            mh = h_block;
        }
    });
    jQuery(".printing-category-grid-container .col-6").css('height', mh);

    if (jQuery(window).width() > 1024) {
        //equal height product-page

        var mhprod = 0;
        jQuery(".catalog-product-view .right-product-view").each(function () {
            var h_block = parseInt(jQuery(this).height());
            if (h_block > mhprod) {
                mhprod = h_block;
            }
        });
        jQuery(".catalog-product-view .left-product-view").height(mhprod);

        //equal height checkout-page


        var mhcols = 0;
        jQuery(".checkout-onepage-index .col-main").each(function () {
            var h_block = parseInt(jQuery(this).height());
            if (h_block > mhcols) {
                mhcols = h_block;
            }
        });
        jQuery(".checkout-onepage-index .col-right").css('min-height', mhcols);


    }

    jQuery('.order-advance-checked').hide();
    jQuery('#order-advance-label').click(function () {
        if (jQuery('#order-advance').is(':checked')) {
            jQuery('.order-advance-checked').slideUp();
        }
        else {
            jQuery('.order-advance-checked').slideDown();
        }
    });

};

