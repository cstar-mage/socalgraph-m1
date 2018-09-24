function Shipping(settings) {
    this.settings = settings;

    var that = this;
    jQuery('a>.ship-address').click(function (event) {
        event.preventDefault();
        that.addNewAddress();
    });

    jQuery('.ship-address-container').on('click', '.remove', function () {
        jQuery(this).closest('.shipping-address-item').remove();
    });
}
Shipping.prototype = {
    addNewAddress: function () {
        addressbook.show(this.submitAddress, true)
    },
    submitAddress: function (selectedItems) {
        selectedItems.each(function () {
            var selectedItem = jQuery(this);
            var item = jQuery('<div class="shipping-address-item"><input name="shipping[]" type="hidden"><div class="text"></div><div class="remove">Remove</div></div>');
            item.find('.text').html(selectedItem.data('text'));
            item.find('input').val(selectedItem.val());
            jQuery('.ship-address-container').append(item);
        });
        return true;
    }
};