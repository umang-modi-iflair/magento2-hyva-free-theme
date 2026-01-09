define([
    'Magento_Ui/js/modal/alert'
], function (alert) {
    'use strict';

    return function (HtmlCode) {
        return HtmlCode.extend({
            initialize: function () {
                this._super();
                this.elementTmpl = 'Iflair_PageBuilder/form/element/html-code-custom';
                return this;
            },

            clickCustombutton: function () {
                alert({
                    content: 'Custom button clicked!'
                });
                return;
            }
        });
    };
});