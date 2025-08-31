require([
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'mage/translate'
], function (validator, $) {
    'use strict';

    validator.addRule(
        'attribute_code',
        function (value) {
            // Ensure the first character is a letter (A-Z or a-z)
            if (!/^[a-zA-Z]/.test(value)) {
                return false;
            }

            // Ensure the entire string contains only letters, numbers, and underscores (_)
            if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                return false;
            }

            return true; // If all conditions are met
        },
        $.mage.__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in this field, and the first character should be a letter.')
    );
});
