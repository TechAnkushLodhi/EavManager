define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            var initialValue = this._super().initialValue;
            this.updateFieldVisibility(initialValue);
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.updateFieldVisibility(value);
            return this._super();
        },

        /**
         * Update field visibility based on the selected value.
         *
         * @param {String} value
         */
        updateFieldVisibility: function (value) {
            // Retrieve fields using uiRegistry.async to ensure they are initialized
            var fieldNames = ['default_value', 'date', 'textarea', 'yes_no', 'color_picker'];
        
            fieldNames.forEach(function (fieldName) {
                uiRegistry.async('index = ' + fieldName)(function (field) {
                    if (field) {
                        field.hide();
                    }
                });
            });
        
            // Show the relevant field based on the selected value
            switch (value) {
                case 'textarea':
                    uiRegistry.async('index = textarea')(function (field) {
                        field && field.show();
                    });
                    break;
                case 'boolean':
                    uiRegistry.async('index = yes_no')(function (field) {
                        field && field.show();
                    });
                    break;    
                case 'date':
                    uiRegistry.async('index = date')(function (field) {
                        field && field.show();
                    });
                    break;
                case 'color_picker':
                    uiRegistry.async('index = color_picker')(function (field) {
                        field && field.show();
                    });
                    break;
                case 'text':
                    uiRegistry.async('index = default_value')(function (field) {
                        field && field.show();
                    });
                    break;
                default:
                    console.warn('No matching field for value:', value);
            }
        
            return this;
        }
        
    });
});
