var PopupNotification = new function () {
    var show = function (data, _class, delay, fadeOutTime) {
        var popup= jQuery('<div>');
        popup.append(data);
        popup.addClass(_class);
        jQuery('body').append(popup);
        popup.css("position","fixed");
        popup.css("top", '1em');
        popup.css("right", '1em');

        popup.delay(delay).fadeOut(fadeOutTime);
    };
    this.success = function (data) {
        show(data,'success-message', 2000, 3000);
    };
    this.error = function (data) {
        show(data,'error-message', 2000, 3000);
    };
    this.confirm = function(message, callback) {
        var modal = jQuery('#popup-confirm-modal');
        modal.find('.confirm-message').html(message);
        modal.find('.confirm.button').off('click').click(function () {
            modal.modal('hide');
            callback();
        });
        modal.modal('show');
    }
};
