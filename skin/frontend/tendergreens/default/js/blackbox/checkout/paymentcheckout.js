var PaymentCheckout = Class.create();
PaymentCheckout.prototype = {
    initialize: function (forms, saveUrl, successUrl) {
        this.loadWaiting = false;
        this.forms = forms;
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;

        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },

    ajaxFailure: function(){
        location.href = encodeURI(this.failureUrl);
    },

    save: function () {
        if (!this.validate()) {
            return;
        }

        var elements = [];
        for (var i = 0; i < this.forms.length; i++) {
            elements = Array.prototype.concat(elements, Form.getElements(this.forms[i]));
        }

        this.setLoadWaiting(true);

        new Ajax.Request(
            this.saveUrl,
            {
                method: 'post',
                onComplete: this.onComplete,
                onSuccess: this.onSave,
                onFailure: this.ajaxFailure.bind(checkout),
                parameters: Form.serializeElements(elements)
            }
        )
    },

    _disableEnableAll: function(element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },

    setLoadWaiting: function(step, keepDisabled) {
        var container;
        if (step) {
            if (this.loadWaiting) {
                this.setLoadWaiting(false);
            }
            container = $('button-save');
            container.addClassName('disabled');
            container.setStyle({opacity:.5});
            this._disableEnableAll(container, true);
            Element.show('load-please-wait');
        } else {
            if (this.loadWaiting) {
                container = $('button-save');
                var isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.removeClassName('disabled');
                    container.setStyle({opacity:1});
                }
                this._disableEnableAll(container, isDisabled);
                Element.hide('load-please-wait');
            }
        }
        this.loadWaiting = step;
    },

    resetLoadWaiting: function(transport){
        this.setLoadWaiting(false, this.isSuccess);
    },

    validate: function () {
        var result = true;
        var validator;
        for (var i = 0; i < this.forms.length; i++) {
            validator = new Validation(this.forms[i]);
            result &= validator.validate();
        }

        return result;
    },

    nextStep: function(transport){
        if (transport) {
            var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

            if (response.redirect) {
                this.isSuccess = true;
                location.href = encodeURI(response.redirect);
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                location.href = encodeURI(this.successUrl);
            }
            else{
                var msg = response.message || response.error;
                if (Object.isArray(msg)) {
                    msg = msg.join("\n").stripTags().toString();
                }
                if (msg) {
                    alert(msg);
                }
            }
        }
    },
};

// payment
var Payment = Class.create();
Payment.prototype = {
    beforeInitFunc:$H({}),
    afterInitFunc:$H({}),
    beforeValidateFunc:$H({}),
    afterValidateFunc:$H({}),
    initialize: function(form){
        this.form = form;
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },

    addBeforeInitFunction : function(code, func) {
        this.beforeInitFunc.set(code, func);
    },

    beforeInit : function() {
        (this.beforeInitFunc).each(function(init){
            (init.value)();
        });
    },

    init : function () {
        this.beforeInit();
        var elements = Form.getElements(this.form);
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]' || elements[i].name == 'form_key') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) this.switchMethod(method);
        this.afterInit();
    },

    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    afterInit : function() {
        (this.afterInitFunc).each(function(init){
            (init.value)();
        });
    },

    switchMethod: function(method){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            $('payment_form_'+this.currentMethod).fire('payment-method:switched-off', {method_code : this.currentMethod});
        }
        if ($('payment_form_'+method)){
            this.changeVisible(method, false);
            $('payment_form_'+method).fire('payment-method:switched', {method_code : method});
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code : method});
        }
        if (method == 'free' && quoteBaseGrandTotal > 0.0001
            && !(($('use_reward_points') && $('use_reward_points').checked) || ($('use_customer_balance') && $('use_customer_balance').checked))
        ) {
            if ($('p_method_' + method)) {
                $('p_method_' + method).checked = false;
                if ($('dt_method_' + method)) {
                    $('dt_method_' + method).hide();
                }
                if ($('dd_method_' + method)) {
                    $('dd_method_' + method).hide();
                }
            }
            method == '';
        }
        if (method) {
            this.lastUsedMethod = method;
        }
        this.currentMethod = method;
    },

    changeVisible: function(method, mode) {
        var block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].each(function(el) {
            element = $(el);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                element.select('input', 'select', 'textarea', 'button').each(function(field) {
                    field.disabled = mode;
                });
                jQuery(element).find('.custom_select').selectmenu("option", "disabled", mode);
            }
        });
    },

    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    beforeValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    validate: function() {
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.').stripTags());
            return false;
        }
        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        alert(Translator.translate('Please specify payment method.').stripTags());
        return false;
    },

    addAfterValidateFunction : function(code, func) {
        this.afterValidateFunc.set(code, func);
    },

    afterValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    resetLoadWaiting: function(){
        checkout.setLoadWaiting(false);
    }
};