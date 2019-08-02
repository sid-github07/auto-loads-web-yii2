/* global vatCodeInputIds */

/**
 * Returns currently active VAT code
 *
 * @param {string} inputId Current VAT code input ID
 * @returns {*|jQuery}
 */
function getActiveVatCode(inputId) {
    return $('.vat-code-list-' + inputId).find('li.active').data('code');
}

/**
 * Changes selected VAT code button value to currently selected VAT code
 *
 * @param {string} inputId Current VAT code input ID
 * @param {string} selectedCode Currently selected VAT code
 */
function changeSelectedVatCode(inputId, selectedCode) {
    /** @member {string} Current VAT code input active VAT code */
    var activeVatCode = getActiveVatCode(inputId);
    /** @member {string} Flag icon class name that needs to be removed */
    var oldClass = 'flag-icon-' + activeVatCode.toLowerCase();
    /** @member {string} Flag icon class name that needs to be added */
    var newClass = 'flag-icon-' + selectedCode.toLowerCase();

    $('#vat-code-' + inputId + ' .' + oldClass).removeClass(oldClass).addClass(newClass);
    $('.active-vat-code-' + inputId).val(selectedCode.toUpperCase());
    $('#' + inputId).val(selectedCode.toUpperCase());
}

/**
 * Changes active VAT code to currently selected VAT code
 *
 * @param {string} inputId Current VAT code input ID
 * @param {string} selectedCode Currently selected VAT code
 */
function changeActiveVatCode(inputId, selectedCode) {
    /** @member {object} VAT code list selector */
    var listSelector = $('.vat-code-list-' + inputId);

    listSelector.find('li.active').removeClass('active');
    listSelector.find(".vat-code-item-" + inputId + "[data-code='" + selectedCode + "']").addClass('active');
}

/**
 * Executes anonymous function when user selects specific VAT code from drop down list menu
 */
$(document).ready(function () {
    $.each(vatCodeInputIds, function (key, inputId) {
        $('.vat-code-item-' + inputId).click(function (e) {
            e.preventDefault(); // Prevents from scrolling to top
            /** @member {string} Currently selected VAT code */
            var selectedCode = $(this).data('code');

            changeSelectedVatCode(inputId, selectedCode);
            changeActiveVatCode(inputId, selectedCode);
        });

        /**
         * Validates current VAT code input field when user blurred the input
         */
        $('#' + inputId).blur(function () {
            /** @member {string} Form ID of current input field */
            var formId = $('#' + inputId).get(0).form.id;
            /** @member {string} VAT code value */
            var vatCode = $(this).val() != '' ? $(this).val() : '';
            /** @member {string} Current VAT code input field name */
            var name = $(this).attr('name');
            /** @member {object} Current VAT code input field value */
            var data = makeValidationData(name, vatCode);
            /** @member {string} Current VAT code input model name in lowercase and dashes instead of spaces */
            var model = getModelByName(name);
            /** @member {string} Current VAT code input attribute name */
            var attribute = getAttributeByName(name);

            addValidation(formId, inputId, model, attribute);
            validate(formId, inputId, data, model, attribute);
        });
    });
});