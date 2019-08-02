/* global
LOAD_TYPE,
TYPE_PARTIAL,
TYPE_FULL,
QUANTITY_MAX_VALUE,
MODEL_MAX_LENGTH,
MODEL_CHARACTERS_LEFT,
FOR_CAR_MODEL,
FOR_ALL_LOAD,
TOTAL_QUANTITY_TOO_BIG,
loadPage
*/

/**
 * Toggles remove buttons visibility
 */
function toggleRemoveButtonsVisibility() {
    if ($('.load-cars-editing-item').length > 1) {
        $('.remove-load-car-model').removeClass('hidden');
    } else {
        $('.remove-load-car-model').addClass('hidden');
    }
}

/**
 * Toggles load cars models and quantities elements required class
 */
function toggleRequiredClass() {
    var loadType = $('.IA-C-2.IA-C-3:checked').val();
    if (isPartial(loadType)) {
        $('.field-loadcar-model, .field-loadcar-quantity').addClass('required');
    } else {
        $('.field-loadcar-model, .field-loadcar-quantity').removeClass('required');
    }
}

/**
 * Toggles load price and load cars prices elements visibility
 *
 * @param paymentMethod
 */
function togglePriceVisibility(paymentMethod) {
    if (paymentMethod == undefined) {
        paymentMethod = $('#IA-C-4').val();
    }

    if (isForAllLoad(paymentMethod)) {
        $('.load-price').removeClass('hidden');
        $('.car-price').addClass('hidden');
    } else {
        $('.load-price').addClass('hidden');
        $('.car-price').removeClass('hidden');
    }
}

/**
 * Toggles load cars quantities visibility
 *
 * @param {undefined|number} paymentMethod Payment method for load
 */
function toggleQuantityVisibility(paymentMethod) {
    if (paymentMethod == undefined) {
        paymentMethod = $('#IA-C-4').val();
    }

    if (isFull() && isForCarModel(paymentMethod)) {
        $('.car-quantity').addClass('hidden');
    } else {
        $('.car-quantity').removeClass('hidden');
    }
}

/**
 * Validates load cars quantities
 *
 * This is custom validation, to check whether total number of load cars quantities is not larger than limit
 */
function validateQuantity() {
    var formId = '#load-editing-form';
    if ($('#load-editing-form').length == 0) {
        formId = '#load-announcement-form';
    }

    var quantities = countCarQuantities();
    $.each($('.car-quantity select'), function (index) {
        var id = 'loadcar-' + index + '-quantity';
        if (quantities > QUANTITY_MAX_VALUE) {
            $(formId).yiiActiveForm('updateAttribute', id, [TOTAL_QUANTITY_TOO_BIG]);
        } else {
            $(formId).yiiActiveForm('updateAttribute', id, '');
        }
    });
}

/**
 * Counts load cars quantities
 *
 * @returns {number}
 */
function countCarQuantities() {
    var quantities = 0;
    $.each($('.car-quantity select'), function (index, element) {
        quantities =  Number(quantities) + Number($(element).val());
    });
    return quantities;
}

/**
 * Toggles currently editable load elements structure
 *
 * @param {undefined|number} paymentMethod Payment method for load
 */
function toggleEditableLoadElementsStructure(paymentMethod) {
    if (paymentMethod == undefined) {
        paymentMethod = $('#IA-C-4').val();
    }

    if (isForAllLoad(paymentMethod)) {
        $('.load-payment-method-selection').removeClass('col-xs-12').addClass('col-sm-6');
        $('.load-price').addClass('col-sm-6');

        // Car model items structure
        if (isVisibleRemoveCarButton()) {
            $('.car-quantity').removeClass('col-lg-2 col-md-5 col-sm-5 col-md-3 col-sm-3 col-lg-4 col-md-4 col-sm-4 hidden').addClass('col-lg-3 col-md-3 col-sm-3');
            $('.car-model, .car-state').removeClass('col-lg-3 col-sm-6').addClass('col-sm-4');
        } else {
            $('.car-quantity').removeClass('col-lg-3 col-md-3 col-sm-3 col-md-6 col-sm-6 hidden').addClass('col-lg-4 col-md-4 col-sm-4');
            $('.car-model, .car-state').removeClass('col-lg-3 col-sm-6').addClass('col-sm-4');
            $('.car-price').removeClass('col-lg-3 col-sm-6');
        }
    } else {
        $('.load-payment-method-selection').addClass('col-xs-12').removeClass('col-sm-6');
        $('.load-price').removeClass('col-sm-6');

        // Car model items structure
        if (isFull()) {
            if (isVisibleRemoveCarButton()) {
                $('.car-price').removeClass('col-sm-4 hidden').addClass('col-sm-3');
            } else {
                $('.car-quantity').removeClass('col-sm-4');
                $('.car-model, .car-state').removeClass('col-sm-4').addClass('col-sm-4');
                $('.car-price').removeClass('col-lg-3 col-md-3 col-sm-3 col-lg-4 col-md-4 col-sm-4 hidden').addClass('col-sm-4');
            }
        } else {
            if (isVisibleRemoveCarButton()) {
                $('.car-quantity').removeClass('col-lg-3 col-lg-4 col-md-4 col-sm-4 col-md-6 col-md-3 col-sm-3 col-sm-6').addClass('col-lg-2 col-sm-6');
                $('.car-model, .car-state').removeClass('col-sm-4').addClass('col-lg-3 col-sm-6');
                $('.car-price').removeClass('col-sm-4').addClass('col-lg-3 col-sm-5');
            } else {
                $('.car-quantity').removeClass('col-lg-2 col-md-6 col-sm-6 col-lg-4 col-md-4 col-sm-4').addClass('col-lg-3 col-sm-6');
                $('.car-model, .car-state').removeClass('col-sm-4').addClass('col-lg-3 col-sm-6');
                $('.car-price').removeClass('col-lg-4 col-md-5 col-sm-5 col-md-4 col-sm-4 hidden').addClass('col-lg-3 col-sm-6');
            }
        }
    }
}

/**
 * Changes load and load cars elements visibility on payment method change
 *
 * @param element
 */
function changeElementsVisibility(element) {
    var paymentMethod = $(element).val();

    toggleRequiredClass();
    togglePriceVisibility(paymentMethod);
    toggleQuantityVisibility(paymentMethod);
    validateQuantity();
    toggleEditableLoadElementsStructure(paymentMethod);
}

/**
 * Prints how many characters are left to enter in car model input
 *
 * @param {object} element This object
 */
function printRemainingCharacters(element) {
    var characters = $(element).val().length;
    var leftSymbols = MODEL_MAX_LENGTH - characters;

    if (characters <= MODEL_MAX_LENGTH) {
        $(element).parent().find('.help-block').text(MODEL_CHARACTERS_LEFT + leftSymbols);
    }
}

/**
 * Checks whether load type is partial
 *
 * @param {number} loadType Load type
 * @returns {boolean}
 */
function isPartial(loadType) {
    if (loadType == undefined) {
        loadType = LOAD_TYPE;
    }

    return loadType == TYPE_PARTIAL;
}

/**
 * Checks whether load type is full
 *
 * @param {number} loadType Load type
 * @returns {boolean}
 */
function isFull(loadType) {
    if (loadType == undefined) {
        loadType = LOAD_TYPE;
    }
    return loadType == TYPE_FULL;
}

/**
 * Checks whether load payment method is for car model
 *
 * @param {number} paymentMethod Load payment method
 * @returns {boolean}
 */
function isForCarModel(paymentMethod) {
    return paymentMethod == FOR_CAR_MODEL;
}

/**
 * Checks whether load payment method is for all load
 *
 * @param {number} paymentMethod Load payment method
 * @returns {boolean}
 */
function isForAllLoad(paymentMethod) {
    return paymentMethod == FOR_ALL_LOAD;
}

/**
 * Checks whether remove car button is visible
 *
 * @returns {*|jQuery}
 */
function isVisibleRemoveCarButton() {
    return $('.remove-load-car-model').is(':visible');
}

/**
 * Checks whether load type is selected
 *
 * @returns {*|jQuery}
 */
function isLoadTypeSelected() {
    return $('.IA-C-2.IA-C-3').is(':checked');
}

/**
 * Saves edited load information
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edited information needs to be saved
 * @param {object} element This object
 */
function editLoad(e, id, element) {
    e.preventDefault();
    e.stopImmediatePropagation(); // Prevents form from double submit
    var $form = $('#load-editing-form');
    var data = $form.data("yiiActiveForm");
    $.each(data.attributes, function() {
        this.status = 2;
    });
    $form.yiiActiveForm("validate");
    if(!$('#load-editing-form').find('.has-error').length) {
        $.pjax({
            type: 'POST',
            url: appendUrlParams($(element).attr('action')),
            data: {
                id: id,
                data: $(element).serialize()
            },
            container: '#my-loads-table-pjax',
            push: false,
            replace: false,
            scrollTo: false
        }).done(function () {
            $('#edit-load-modal').modal('hide');
            $.pjax.reload({container: '#toastr-pjax'});
        });
    }
}

/**
 * Toggles new load form elements structure
 */
function toggleNewLoadElementsStructure() {
    var loadType = $('.IA-C-2.IA-C-3:checked').val();
    var paymentMethod = $('#IA-C-4').val();

    removeNewLoadElementsClasses();

    // Load

    if (isForCarModel(paymentMethod)) {
        $('.load-price-container').addClass('hidden');
        $('.field-IA-C-4').removeClass('col-sm-6');
    }

    if (isForAllLoad(paymentMethod)) {
        $('.field-IA-C-4').addClass('col-sm-6');
        $('.load-price-container').addClass('col-sm-6 col-xs-12');
    }

    // Load cars

    if (!isVisibleRemoveCarButton() && (!isLoadTypeSelected() || isPartial(loadType)) && isForCarModel(paymentMethod)) {
        $('.field-loadcar-quantity, .field-loadcar-model, .field-loadcar-price, .field-loadcar-state').addClass('col-lg-3 col-sm-6');
    }

    if (!isVisibleRemoveCarButton() && isForAllLoad(paymentMethod)) {
        $('.field-loadcar-quantity, .field-loadcar-model, .field-loadcar-state').addClass('col-sm-4');
        $('.field-loadcar-price').addClass('hidden');
    }

    if (!isVisibleRemoveCarButton() && isFull(loadType) && isForCarModel(paymentMethod)) {
        $('.field-loadcar-quantity').addClass('hidden');
        $('.field-loadcar-model, .field-loadcar-price, .field-loadcar-state').addClass('col-sm-4');
    }

    if (isVisibleRemoveCarButton() && (!isLoadTypeSelected() || isPartial(loadType)) && isForCarModel(paymentMethod)) {
        $('.field-loadcar-quantity, .field-loadcar-model, .field-loadcar-state').addClass('col-lg-3 col-sm-6');
        $('.field-loadcar-price').addClass('col-lg-2 col-sm-5');
    }

    if (isVisibleRemoveCarButton() && isForAllLoad(paymentMethod)) {
        $('.field-loadcar-quantity').addClass('col-sm-3');
        $('.field-loadcar-model, .field-loadcar-state').addClass('col-sm-4');
        $('.field-loadcar-price').addClass('hidden');
    }

    if (isVisibleRemoveCarButton() && isFull(loadType) && isForCarModel(paymentMethod)) {
        $('.field-loadcar-quantity').addClass('hidden');
        $('.field-loadcar-model, .field-loadcar-state').addClass('col-sm-4');
        $('.field-loadcar-price').addClass('col-sm-3');
    }
}

/**
 * Removes new load elements classes
 */
function removeNewLoadElementsClasses() {
    $('.load-unload-city-container').removeClass('col-sm-4 col-lg-3 col-sm-6');
    $('.load-date-container').removeClass('col-sm-4 col-lg-3 col-sm-6');
    $('.load-price-container').removeClass('hidden col-lg-3 col-sm-6');

    $('.field-loadcar-quantity').removeClass('col-lg-2 col-lg-3 col-sm-6 col-sm-4 hidden col-sm-3');
    $('.field-loadcar-model').removeClass('col-lg-3 col-sm-6 col-sm-4 col-sm-3');
    $('.field-loadcar-price').removeClass('col-lg-3 col-sm-6 hidden col-sm-4 col-lg-2 col-sm-5 col-sm-3');
    $('.field-loadcar-state').removeClass('col-lg-3 col-sm-6 col-sm-4');
}