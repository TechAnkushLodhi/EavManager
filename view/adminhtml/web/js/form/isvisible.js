define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';

    return select.extend({

        initialize: function () {
            this._super();

            // Initial value se visibility set karo
            this.updateFieldVisibility(this.value());

            // On change bhi listener lagao
            this.value.subscribe(function (newValue) {
                this.updateFieldVisibility(newValue);
            }.bind(this));

            return this;
        },

        /**
         * Enable/Disable target field based on this field's value
         */
          updateFieldVisibility: function (value) {
            uiRegistry.async('index = store_view')(function (field) {
                if (field) {
                    field.disabled(value === '0'); // true if 0, false if 1
                }
            });

            return this;
        }

    });
});
