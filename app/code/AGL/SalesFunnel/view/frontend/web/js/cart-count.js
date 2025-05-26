define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate',
    'ko'
], function (Component, customerData, $t, ko) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'AGL_SalesFunnel/cart-count'
        },
        initialize: function () {
            this._super();
            var self = this;
            var cartCountSection = customerData.get('agl_cart_count');
            this.aglCartCountText = ko.observable('');
            function updateText() {
                var count = cartCountSection() && cartCountSection().count ? cartCountSection().count : 0;
                self.aglCartCountText($t('%1 AGL customers love this product!').replace('%1', count));
            }
            cartCountSection.subscribe(updateText);
            updateText();
            return this;
        }
    });
}); 