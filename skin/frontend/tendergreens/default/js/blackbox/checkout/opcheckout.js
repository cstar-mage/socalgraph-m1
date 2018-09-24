Billing.addMethods({
    selectAddress: function () {
        var form = jQuery('#co-billing-form');
        addressbook.show(function (selectedItem) {
            var data = selectedItem.data('json');
            form.find('#billing\\:storelocator_id').val(data.storelocator_id);
            form.find('#billing\\:customer_address_id').val(data.entity_id);
            form.find('.text').html(selectedItem.data('text'));
            form.show();
        });
    },
    changeAddress: function () {
        var form = jQuery('#co-billing-form');
        addressbook.show(function (selectedItem) {
            var selector = '#billing\\:';
            form.find('input.auto, select.auto').val('');

            var data = selectedItem.data('json');
            for (var property in data) {
                if (data.hasOwnProperty(property)) {
                    var elem = form.find(selector + property).val(data[property]);
                    if (elem.length) {
                        elem[0].simulate('change');
                    }
                }
            }
        }, false);
    }
});
Shipping.addMethods({
    updateFormQty: function (updateIndexes) {
        var shipments = jQuery('#shipping-forms .co-shipping');

        if (shipments.length > 1) {
            shipments.find(".shipping-products, .remove").removeClass("hide");
        } else {
            shipments.find(".shipping-products, .remove").addClass("hide");
        }

        var idRegex = /^shipping\d+(.*)$/;
        var nameRegex = /^shipping\[\d+\](.*)$/;
        var result;

        if (updateIndexes) {
            shipments.each(function(i, e) {
                var shipment = jQuery(e);
                shipment.find('form').attr('id', 'co-shipping-form-' + i);
                shipment.find('.shipping-number').html(i + 1);
                shipment.find('.shipping-new-address-form').attr('id', 'shipping' + i + ':new-address-form');
                shipment.find('input, select, textarea').each(function (j, e) {
                    result = idRegex.exec(e.id);
                    if (result && result[0].length) {
                        e.id = 'shipping' + i + result[1];
                    }
                    result = nameRegex.exec(e.name);
                    if (result && result[0].length) {
                        e.name = 'shipping[' + i + ']' + result[1];
                    }
                });
                shipment.find('label').each(function (j, e) {
                   result = idRegex.exec(e.getAttribute('for'));
                    if (result && result[0].length) {
                        e.setAttribute('for', 'shipping' + i + result[1]);
                    }
                });
                shipment.data('id', i);
            });
        } else {
            shipments.each(function(i, e) {
                jQuery(e).find('.shipping-number').html(i + 1);
            });
        }
    },
    setSameAsBilling: function(flag) {
        return;
        $('shipping0:same_as_billing').checked = flag;
// #5599. Also it hangs up, if the flag is not false
//        $('billing:use_for_shipping_yes').checked = flag;
        if (flag) {
            this.syncWithBilling();
        }
    },
    addNewAddress: function() {
        var that = this;
        addressbook.show(function (selectedItems) {
            var shipments = jQuery();
            selectedItems.each(function () {
                var selectedItem = jQuery(this);
                var item = jQuery(that.settings.itemHtml);
                var data = selectedItem.data('json');
                item.find('.text').html(selectedItem.data('text'));
                item.find('input.storelocator-id').val(data.storelocator_id);
                item.find('input.customer-address-id').val(data.entity_id);
                item.find('select.qty').val(0);
                jQuery('#shipping-forms').append(item);
                shipments = shipments.add(item);
            });
            that.updateFormQty(true);

            if (that.initNewAddressCallbacks) {
                shipments.each(function() {
                    for (var i = 0; i < that.initNewAddressCallbacks.length; i++) {
                        that.initNewAddressCallbacks[i](jQuery(this));
                    }
                });
            }

            that.saveAddresses(shipments);
        }, true);
    },
    addInitNewAddressCallback: function (callback) {
        if (!this.initNewAddressCallbacks) {
            this.initNewAddressCallbacks = [];
        }
        this.initNewAddressCallbacks.push(callback);
    },
    changeAddress: function(element) {
        var that = this;
        var item = jQuery(element).closest('.co-shipping');
        addressbook.show(function (selectedItem) {
            var data = selectedItem.data('json');
            item.find('input.storelocator-id').val(data.storelocator_id);
            item.find('input.customer-address-id').val(data.entity_id);
            item.find('.text').html(selectedItem.data('text'));

            var form = item.find('form')[0];
            checkout.setLoadWaiting('shipping');
            new Ajax.Request(
                '/checkout/onepage/saveShippingAddress',
                {
                    method:'post',
                    onComplete: that.onComplete,
                    onSuccess: that.getOnSaveAddress(item),
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(form)
                }
            );
        }, false);
    },
    saveAddresses: function (shipments) {
        if (checkout.loadWaiting!=false) return;

        checkout.setLoadWaiting('shipping');
        new Ajax.Request(
            '/checkout/onepage/saveShippingAddress',
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.getOnSaveAddress(shipments),
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serializeElements(shipments.find('input, select, button, textarea').toArray())
            }
        );
    },
    getOnSaveAddress: function (addresses) {
        return function (transport) {
            var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

            if (response.error){
                if (Object.isString(response.message)) {
                    alert(response.message.stripTags().toString());
                } else {
                    var msg = response.message;
                    if(Object.isArray(msg)) {
                        alert(msg.join("\n"));
                    }
                    alert(msg.stripTags().toString());
                }
                this.removeNotSavedAddresses();

                return false;
            }

            if (response.cart_html) {
                jQuery('.table-cart-item').replaceWith(response.cart_html);
            }
            addresses.each(function (i) {
                jQuery(this).find('.address-id').val(response['address_ids'][i]);
                jQuery(this).addClass('saved');
            });
        }
    },
    removeNotSavedAddresses: function () {
        jQuery('.co-shipping .address-id[value=""]').closest('.co-shipping').remove();
        this.updateFormQty(false);
    },
    updateAddressItemQty: function (e) {
        if (checkout.loadWaiting!=false) return;
        var form = jQuery(e.target).closest('.co-shipping-form')[0];

        var validator = new Validation(form);
        if (validator.validate()) {
            checkout.setLoadWaiting('shipping');
            new Ajax.Request(
                '/checkout/onepage/updateAddressItemQty',
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: function (transport) {
                        var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

                        if (response.error){
                            if (Object.isString(response.message)) {
                                alert(response.message.stripTags().toString());
                            } else {
                                if (window.shippingRegionUpdater) {
                                    shippingRegionUpdater.update();
                                }
                                var msg = response.message;
                                if(Object.isArray(msg)) {
                                    alert(msg.join("\n"));
                                }
                                alert(msg.stripTags().toString());
                            }

                            return false;
                        }

                        if (response.cart_html) {
                            jQuery('.table-cart-item').replaceWith(response.cart_html);
                        }
                    },
                    onFailure: checkout.ajaxFailure.bind(checkout),
                    parameters: Form.serialize(form)
                }
            );
        }
    },
    removeAddress: function (e) {
        if (checkout.loadWaiting!=false) return;
        if (jQuery('#shipping-forms .co-shipping').length > 1) {
            var shipment = jQuery(e.target).parents('.co-shipping');

            var remove = function () {
                shipment.remove();
                shipping.updateFormQty(false);

                var shipments = jQuery('#shipping-forms .co-shipping');
                if (shipments.length == 1) {
                    shipments.find(".shipping-products, .remove").addClass("hide");
                }
            };

            var shippingAddressId = shipment.find('.address-id').val();

            if (shippingAddressId) {
                checkout.setLoadWaiting('shipping');
                new Ajax.Request(
                    '/checkout/onepage/removeShippingAddress',
                    {
                        method:'post',
                        onComplete: this.onComplete,
                        onSuccess: function (transport) {
                            var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

                            if (response.error){
                                if (Object.isString(response.message)) {
                                    alert(response.message.stripTags().toString());
                                } else {
                                    if (window.shippingRegionUpdater) {
                                        shippingRegionUpdater.update();
                                    }
                                    var msg = response.message;
                                    if(Object.isArray(msg)) {
                                        alert(msg.join("\n"));
                                    }
                                    alert(msg.stripTags().toString());
                                }

                                return false;
                            }

                            if (response.cart_html) {
                                jQuery('.table-cart-item').replaceWith(response.cart_html);
                            }

                            remove();
                        },
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: 'address_id=' + shippingAddressId
                    }
                );
            } else {
                remove();
            }
        }
    },
    save: function(){
        if (checkout.loadWaiting!=false) return;

        var targetForm = jQuery('#opc-shipping');

        if (targetForm.find('.co-shipping:not(.saved)').length > 0) {
            alert("Please, select address");
            return;
        }

        checkout.setLoadWaiting('shipping');
        new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serializeElements(targetForm.find('input, select, button, textarea').toArray())
            }
        );
    },
    nextStep: function(transport){
        var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

        if (response.error){
            if (Object.isString(response.message)) {
                alert(response.message.stripTags().toString());
            } else {
                if (window.shippingRegionUpdater) {
                    shippingRegionUpdater.update();
                }
                var msg = response.message;
                if(Object.isArray(msg)) {
                    alert(msg.join("\n"));
                }
                alert(msg.stripTags().toString());
            }

            return false;
        }

        if (response.cart_html) {
            jQuery('.table-cart-item').replaceWith(response.cart_html);
        }

        checkout.setStepResponse(response);

        /*
         var updater = new Ajax.Updater(
         'checkout-shipping-method-load',
         this.methodsUrl,
         {method:'get', onSuccess: checkout.setShipping.bind(checkout)}
         );
         */
        //checkout.setShipping();
    },

    setAddress: function(addressId, formId){
        if (addressId) {
            new Ajax.Request(
                this.addressUrl+addressId,
                {method:'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout)}
            );
        }
        else {
            this.fillForm(false, formId);
        }
    },

    newAddress: function(isNew, formId){
        if (formId == null) {
            formId = '0';
        }
        if (isNew) {
            this.resetSelectedAddress(formId);
            Element.show('shipping' + formId + ':new-address-form');
        } else {
            Element.hide('shipping' + formId + ':new-address-form');
        }
        shipping.setSameAsBilling(false);
    },

    resetSelectedAddress: function(formId){
        var selectElement = $('shipping' + formId + ':address-select');
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport, formId){
        if (formId == null) {
            formId = '0';
        }
        var elementValues = transport.responseJSON || transport.responseText.evalJSON(true) || {};
        if (!transport && !Object.keys(elementValues).length) {
            this.resetSelectedAddress(formId);
        }
        var arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if(arrElements.hasOwnProperty(elemIndex)) {
                if (arrElements[elemIndex].id) {
                    var fieldName = arrElements[elemIndex].id.replace(new RegExp('^shipping' + formId + ':'), '');
                    arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                    if (fieldName == 'country_id' && shippingForm){
                        shippingForm.elementChildLoad(arrElements[elemIndex]);
                    }
                }
            }
        }
    },

//     setSameAsBilling: function(flag, formId) {
//         if (formId == null) {
//             formId = '0';
//         }
//         $('shipping' + formId + ':same_as_billing').checked = flag;
// // #5599. Also it hangs up, if the flag is not false
// //        $('billing:use_for_shipping_yes').checked = flag;
//         if (flag) {
//             this.syncWithBilling();
//         }
//     },

    syncWithBilling: function (formId) {
        if (formId == null) {
            formId = '0';
        }
        $('billing-address-select') && this.newAddress(!$('billing-address-select').value, formId);
        $('shipping:same_as_billing').checked = true;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(new RegExp('^shipping' + formId + ':'), 'billing:'));
                    if (sourceField){
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            //$('shipping:country_id').value = $('billing:country_id').value;
            shippingRegionUpdater.update();
            $('shipping' + formId + ':region_id').value = $('billing:region_id').value;
            $('shipping' + formId + ':region').value = $('billing:region').value;
            //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        } else {
            $('shipping' + formId + ':address-select').value = $('billing-address-select').value;
        }
    },

    setRegionValue: function(formId){
        $('shipping' + formId + ':region').value = $('billing' + formId + ':region').value;
    }
});

ShippingMethod.addMethods({
    validate: function() {
        var groups = jQuery('.address-sp-methods');
        if (groups.length == 0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
            return false;
        }

        for (var i = 0; i < groups.length; i++) {
            var methods = jQuery(groups[i]).find('.shipping_method');
            if (methods.length == 0) {
                alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
                return false;
            }
            if (methods.filter(':checked').length == 0) {
                alert(Translator.translate('Please specify shipping method.').stripTags());
                return false;
            }
        }

        return this.validator.validate();
    },
    nextStep: function(transport){
        var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

        if (response.error) {
            alert(response.message.stripTags().toString());
            return false;
        }

        if (response.update_section) {
            $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
        }

        if (typeof payment !== 'undefined') {
            payment.initWhatIsCvvListeners();
        }

        if (response.goto_section) {
            checkout.gotoSection(response.goto_section, true);
            checkout.reloadProgressBlock();
            return;
        }

        if (response.payment_methods_html) {
            $('checkout-payment-method-load').update(response.payment_methods_html);
        }

        checkout.setShippingMethod();
    }
});

Review.addMethods({
    save: function(){
        if (checkout.loadWaiting!=false) return;
        checkout.setLoadWaiting('review');
        var params;
        if (typeof payment !== 'undefined') {
            params = Form.serialize(payment.form);
            if (this.agreementsForm) {
                params += '&'+Form.serialize(this.agreementsForm);
            }
        } else {
            if (this.agreementsForm) {
                params = Form.serialize(this.agreementsForm);
            } else {
                params = '';
            }
        }
        params.save = true;
        new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                parameters:params,
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout)
            }
        );
    }
});

function execBodyScript(body_el) {
    // Finds and executes scripts in a newly added element's body.
    // Needed since innerHTML does not run scripts.
    //
    // Argument body_el is an element in the dom.

    function nodeName(elem, name) {
        return elem.nodeName && elem.nodeName.toUpperCase() ===
            name.toUpperCase();
    };

    function evalScript(elem) {
        var data = (elem.text || elem.textContent || elem.innerHTML || "" ),
            head = document.getElementsByTagName("head")[0] ||
                document.documentElement,
            script = document.createElement("script");

        script.type = "text/javascript";
        try {
            // doesn't work on ie...
            script.appendChild(document.createTextNode(data));
        } catch(e) {
            // IE has funky script nodes
            script.text = data;
        }

        head.insertBefore(script, head.firstChild);
        head.removeChild(script);
    };

    // main section of function
    var scripts = [],
        script,
        children_nodes = body_el.childNodes,
        child,
        i;

    jQuery(body_el).find('script').each(function() {
        if (!this.type || this.type.toLowerCase() === "text/javascript") {
            scripts.push(this);
        }
    });

    /*for (i = 0; children_nodes[i]; i++) {
     child = children_nodes[i];
     if (nodeName(child, "script" ) &&
     (!child.type || child.type.toLowerCase() === "text/javascript")) {
     scripts.push(child);
     }
     }*/

    for (i = 0; scripts[i]; i++) {
        script = scripts[i];
        if (script.parentNode) {script.parentNode.removeChild(script);}
        evalScript(scripts[i]);
    }
}