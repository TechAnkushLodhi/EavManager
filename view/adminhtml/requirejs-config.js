var config = {
    map: {
        '*': {
            'IcecubeEavValidation': 'Icecube_EavManager/js/form/eavmanager-validation'
        }
    },
    shim: {
        'IcecubeEavValidation': {
            deps: ['jquery', 'mage/validation']
        }
    }
};
