$(document).ready(function () {
    $('.header_user').on('click', function () {
        $('.header_user-menu').slideToggle()
    });

    $('.header_burger').on('click', function () {
        $('body').toggleClass('leftpanel-opened')
    });

    $('.aside_mobile_bg').on('click', function () {
        $('body').removeClass('leftpanel-opened')
    });

    $('.rightpanel-opener').on('click', function () {
        $('.main_section').toggleClass('rightpanel-opened')
    });

    $('.rightpanel-mobilebg').on('click', function () {
        $('.main_section').removeClass('rightpanel-opened')
    });

    $('.aside_list-list .checkbox').on('change', function () {
        $(this).parent().toggleClass('checked');
    });

    $('.aside_list-title').on('click', function () {
        $(this).parent().children('.aside_list-list').slideToggle();
        $(this).parent().toggleClass('active');
    });

    $('.category_page-images').masonry({
        itemSelector: '.category_page-img',
        columnWidth: '.category_page-img-grid',
        horizontalOrder: true,
    })

    $('.catalog2-star').on('click', function () {
        $(this).toggleClass('selected');
    });

    $('.catalog2-star').on({
        mouseenter: function () {
            $('a.catalog2_item_icon').on('click', function () {
                return false;
            });
        },
        mouseleave: function () {
            $('a.catalog2_item_icon').off('click');
        }
    });

    $('.catalog2_item_menu label.checkbox').on('click', function () {
        if ($(this).children('input').prop('checked')) {
            $(this).children('input').prop('checked', false);
            $(this).closest('.catalog2_item').removeClass('selected');
        } else {
            $(this).children('input').prop('checked', true);
            $(this).closest('.catalog2_item').addClass('selected')
            $('.catalog2_item').addClass('menuvisible')
        }
        if ($('.selected').length < 1) {
            $('.catalog2_item').removeClass('menuvisible')
        };
    });
    
    $('.catalog2_item_menu-opener').on('click', function () {
        $(this).toggleClass('opened');
        $(this).parent().toggleClass('menuopened');
        $(this).parent().children('.catalog2_menu').slideToggle();
    });
});

$(window).on('load resize', function () {
    $('.main-content').css('min-height', $(window).height() - $('header').height() + 'px');
    
});

