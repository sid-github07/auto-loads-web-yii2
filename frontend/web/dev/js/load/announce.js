/* global
MODEL_MAX_LENGTH,
MODEL_CHARACTERS_LEFT,
TYPE_PARTIAL,
FOR_ALL_LOAD,
FOR_CAR_MODEL,
TYPE_FULL,
LoadCar
*/

/**
 * Executes anonymous function when document fully loads
 */
$(document).ready(function () {
    formId = 'load-announce-form';
    widgetContainer = 'load_car_model_wrapper';
    widgetContainer = 'load_car_model_wrapper';
    widgetBody = 'load-car-model-container';
    widgetItem = 'load-car-model-item';
    registerCarModelValidation();
    registerQuantityValidation();
    registerModelRemainingCharacters();
    registerCarModelElementsVisibilityChange();
    registerAddAndRemoveCarModel();
    registerLoadDirectionChange();
});

/**
 * Registers load car model remaining characters display
 */
function registerModelRemainingCharacters() {
    $('.' + widgetBody).on('keyup', '.model', function() {
        /** @member {number} Typed symbols length in the input */
        var characters = $(this).val().length;
        /** @member {number} Symbols left to type */
        var leftSymbolsToType = MODEL_MAX_LENGTH - characters;

        if (characters <= MODEL_MAX_LENGTH) {
            $(this).parent().find('.help-block').text(MODEL_CHARACTERS_LEFT + leftSymbolsToType);
        }
    });
}

/**
 * Registers car model element visibility change
 */
function registerCarModelElementsVisibilityChange() {
    $('#IA-C-4, .IA-C-2.IA-C-3').change(function() {
        toggleRequiredClass();
        togglePriceVisibility();
        toggleQuantityVisibility();
        toggleQuantityValidation();
        changeLoadElementsStructure();
        changeCarModelItemsStructure();
    });
}

/**
 * Adds or removes required class
 */
function toggleRequiredClass() {
    if (isLoadTypePartial() && (isPaymentMethodForCarModel() || isPaymentMethodForAllLoad())) {
        $('.field-loadcar-model, .field-loadcar-quantity').addClass('required');
    } else {
        $('.field-loadcar-model, .field-loadcar-quantity').removeClass('required');
    }
}

/**
 * Shows or hides load price and load car price depending on payment method
 */
function togglePriceVisibility() {
    for (var index = 0; index < $('.price').length; index++) {
        if (isPaymentMethodForAllLoad()) {
            $('.load-price').removeClass('hidden');
            $('.field-' + LoadCar + '-' + index + '-price').hide();
        } else {
            $('.load-price').addClass('hidden');
            $('.field-' + LoadCar + '-' + index + '-price').show();
        }
    }
}

/**
 * Shows or hides load car quantity depending on payment method and load type
 */
function toggleQuantityVisibility() {
    for (var index = 0; index < $('.quantity').length; index++) {
        if (isPaymentMethodForCarModel() && isLoadTypeFull()) {
            $('.field-' + LoadCar + '-' + index + '-quantity').hide();
        } else {
            $('.field-' + LoadCar + '-' + index + '-quantity').show();
        }
    }
}

/**
 * Changes announce load elements structure
 */
function changeLoadElementsStructure() {
    if ($('#edit-load-form').is(':visible')) {
        if (isPaymentMethodForAllLoad()) {
            $('.load-payment-method-selection').removeClass('col-lg-12 col-md-12 col-sm-12 col-xs-12').addClass('col-lg-6 col-md-6 col-sm-6 col-xs-12');
            $('.field-load-price').addClass('col-lg-6 col-md-6 col-sm-6 col-xs-12');
        } else {
            if ($('#edit-load-form').is(':visible')) {
                $('.load-payment-method-selection').removeClass('col-lg-6 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-12 col-md-12 col-sm-12 col-xs-12');
                $('.field-load-price').removeClass('col-lg-6 col-md-6 col-sm-6 col-xs-12');
            }
        }
    } else {
        if (isPaymentMethodForAllLoad()) {
            $('.field-IA-C-4').addClass('col-sm-6');
            $('.field-load-price').addClass('col-sm-6 col-xs-12');
        } else {
            $('.field-load-date').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
            $('.field-load-price').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
            $('.load-unload-city-container').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
            $('.field-IA-C-4').removeClass('col-sm-6');
        }
    }
}

/**
 * Change announce new car model info structure
 */
function changeCarModelItemsStructure() {
    if ($('#edit-load-form').is(':visible')) {
        if (isPaymentMethodForAllLoad()) {
            if ($('.remove-load-car-model').is(':visible')) {
                $('.field-loadcar-quantity').removeClass('col-lg-2 col-md-5 col-sm-5 col-md-3 col-sm-3 col-lg-4 col-md-4 col-sm-4 col-xs-12 hidden').addClass('col-lg-3 col-md-3 col-sm-3 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
            } else {
                $('.field-loadcar-quantity').removeClass('col-lg-3 col-md-3 col-sm-3 col-md-6 col-sm-6 col-xs-12 hidden').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-price').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
            }
        } else {
            if (isLoadTypeFull()) {
                if ($('.remove-load-car-model').is(':visible')) {
                    $('.field-loadcar-price').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12 hidden').addClass('col-lg-3 col-md-3 col-sm-3 col-xs-12');
                } else {
                    $('.field-loadcar-quantity').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                    $('.field-loadcar-model').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                    $('.field-loadcar-price').removeClass('col-lg-3 col-md-3 col-sm-3 col-lg-4 col-md-4 col-sm-4 col-xs-12 hidden').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                    $('.field-loadcar-state').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                }
            } else {
                if ($('.remove-load-car-model').is(':visible')) {
                    $('.field-loadcar-quantity').removeClass('col-lg-3 col-lg-4 col-md-4 col-sm-4 col-md-6 col-md-3 col-sm-3 col-sm-6 col-xs-12').addClass('col-lg-2 col-md-6 col-sm-6 col-xs-12');
                    $('.field-loadcar-model').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                    $('.field-loadcar-price').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12 hidden').addClass('col-lg-3 col-md-5 col-sm-5 col-xs-12');
                    $('.field-loadcar-state').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                } else {
                    $('.field-loadcar-quantity').removeClass('col-lg-2 col-md-6 col-sm-6 col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                    $('.field-loadcar-model').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                    $('.field-loadcar-price').removeClass('col-lg-4 col-md-5 col-sm-5 col-md-4 col-sm-4 col-xs-12 hidden').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                    $('.field-loadcar-state').removeClass('col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                }
            }
        }
    } else {
        if ($('.remove-load-car-model').is(':visible')) {
            if ((isLoadTypePartial() || !isLoadTypeSelected()) && isPaymentMethodForCarModel()) {
                $('.field-loadcar-quantity').removeClass('col-lg-4 col-md-6 col-sm-6 col-md-4 col-sm-4 col-lg-3 col-md-3 col-sm-3 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-3 col-md-6 col-sm-6 col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-3 col-md-6 col-sm-6 col-lg-4 col-md-4 col-sm-4 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-price').removeClass('col-md-6 col-sm-6 col-md-4 col-sm-4 col-lg-3 col-md-3 col-sm-3 col-xs-12').addClass('col-lg-2 col-md-5 col-sm-5 col-xs-12');
            } else {
                $('.field-loadcar-quantity').removeClass('col-lg-4 col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-3 col-md-3 col-sm-3 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-price').removeClass('col-lg-4 col-md-6 col-sm-6 col-lg-2 col-md-5 col-sm-5 col-xs-12').addClass('col-lg-3 col-md-3 col-sm-3 col-xs-12');
            }
        } else {
            if ((isLoadTypePartial() || !isLoadTypeSelected()) && isPaymentMethodForCarModel()) {
                $('.field-loadcar-quantity').removeClass('col-lg-4 col-md-4 col-sm-4 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-4 col-md-4 col-sm-4 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-4 col-md-6 col-md-4 col-sm-4 col-sm-6 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
                $('.field-loadcar-price').removeClass('col-lg-2 col-md-5 col-md-4 col-sm-4 col-sm-5 col-lg-4 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-3 col-md-6 col-sm-6 col-xs-12');
            } else {
                $('.field-loadcar-quantity').removeClass('col-md-3 col-sm-3 col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-model').removeClass('col-lg-4 col-md-4 col-sm-4 col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-state').removeClass('col-lg-4 col-md-4 col-sm-4 col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
                $('.field-loadcar-price').removeClass('col-md-3 col-sm-3 col-lg-3 col-md-6 col-sm-6 col-xs-12').addClass('col-lg-4 col-md-4 col-sm-4 col-xs-12');
            }
        }
    }
}

/**
 * Initializes actions when car model added or removed
 */
function registerAddAndRemoveCarModel() {
    $('.' + widgetContainer).on('afterInsert', function () {
        toggleRemoveButtonVisibility();
        toggleRequiredClass();
        togglePriceVisibility();
        toggleQuantityVisibility();
        toggleQuantityValidation();
        changeCarModelItemsStructure();
    }).on('afterDelete', function () {
        toggleRemoveButtonVisibility();
        changeCarModelItemsStructure();
        toggleQuantityVisibility();
        toggleQuantityValidation();
    });
}

/**
 * Shows or hides car model "Remove" button, depending on different car model quantity
 */
function toggleRemoveButtonVisibility() {
    if ($('.' + widgetItem).length > 1) {
        $('.remove-load-car-model').removeClass('hidden');
    } else {
        $('.remove-load-car-model').addClass('hidden');
    }
}

/**
 * Checks whether load type is marked as partial
 *
 * @returns {boolean}
 */
function isLoadTypePartial() {
    return $('.IA-C-2.IA-C-3:checked').val() == TYPE_PARTIAL;
}

/**
 * Checks whether payment method is for all load
 *
 * @returns {boolean}
 */
function isPaymentMethodForAllLoad() {
    return $('#IA-C-4').val() == FOR_ALL_LOAD;
}

/**
 * Checks whether payment method is for car model
 *
 * @returns {boolean}
 */
function isPaymentMethodForCarModel() {
    return $('#IA-C-4').val() == FOR_CAR_MODEL;
}

/**
 * Checks whether load type is marked as full
 *
 * @returns {boolean}
 */
function isLoadTypeFull() {
    return $('.IA-C-2.IA-C-3:checked').val() == TYPE_FULL;
}

/**
 * Checks whether load type is selected
 *
 * @returns {*|jQuery}
 */
function isLoadTypeSelected() {
    return $('.IA-C-2.IA-C-3').is(':checked');
}
