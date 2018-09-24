var AjaxCart = new function () {
    this.setContent = function(content, formkey) {
        jQuery('.header-minicart').html(content);
        this.reinitMinicart(formkey);
    };

    this.reinitMinicart = function (formkey) {
        var skipContents = $j('.header-minicart .skip-content');
        var skipLinks = $j('.header-minicart .skip-link');

        skipLinks.on('click', function (e) {
            e.preventDefault();

            var self = $j(this);
            // Use the data-target-element attribute, if it exists. Fall back to href.
            var target = self.attr('data-target-element') ? self.attr('data-target-element') : self.attr('href');

            // Get target element
            var elem = $j(target);

            // Check if stub is open
            var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;

            // Hide all stubs
            skipLinks.removeClass('skip-active');
            skipContents.removeClass('skip-active');

            // Toggle stubs
            if (isSkipContentOpen) {
                self.removeClass('skip-active');
            } else {
                self.addClass('skip-active');
                elem.addClass('skip-active');
            }
        });

        jQuery('#header-cart').on('click', '.skip-link-close', function(e) {
            var parent = $j(this).parents('.skip-content');
            var link = parent.siblings('.skip-link');

            parent.removeClass('skip-active');
            link.removeClass('skip-active');

            e.preventDefault();
        });

        var minicartOptions = {
            formKey: formkey
        };
        Mini = new Minicart(minicartOptions);
        Mini.init();
    };
};