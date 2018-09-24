function AddressBook(id) {
    this.modal = jQuery('#' + id);
    this.submitCallback = null;

    this.multi = false;
}

AddressBook.prototype.updateAddressGrid =  function(container, url) {
    //showLoading();
    jQuery.ajax({
        url: url,
        method: 'POST',
        complete: function () {
            //hideLoading();
        }.bind(this),
        success: function (e) {
            container.replace(e);
        }
    });
};

AddressBook.prototype.addressGridPagerLinkClick = function(link, event) {
    event.preventDefault();
    this.updateAddressGrid(jQuery(link).closest('.address-select-table-container')[0], link.href);
};

AddressBook.prototype.addressGridPagerInputOnchange = function(input, event) {
    var newValue = input.value;
    input = jQuery(input);

    var maxValue = input.data('last-page-num');

    if (newValue < 1 || newValue > maxValue) {
        input.val('');
        return;
    }

    url = input.data('url') + '?p=' + newValue;

    this.updateAddressGrid(input.closest('.address-select-table-container')[0], url);
};

AddressBook.prototype.addressGridSearch = function(input) {
    jqinput = jQuery(input);
    url = jqinput.data('url') + '?q=' + encodeURIComponent(input.value);
    this.updateAddressGrid(jqinput.closest('.address-select-table-container')[0], url);
};

AddressBook.prototype.submit = function() {
    var selectedItem = this.modal.find('input[type=' + (this.multi ? 'checkbox' : 'radio') + ']:checked');
    if (!selectedItem.length) {
        alert('Please, select address.');
        return;
    }

    this.submitCallback(selectedItem);
    this.modal.modal('hide');
};

AddressBook.prototype.updateMulti = function(multi) {
    var type;
    this.multi = multi;
    if (multi) {
        type = 'checkbox';
    } else {
        type = 'radio';
    }
    this.modal.find('.addressbook-item').each(function () {
        this.type = type;
    });
};

AddressBook.prototype.show = function(submitCallback, multi = false) {
    this.updateMulti(multi);
    this.submitCallback = submitCallback;
    this.modal.modal('show');
};