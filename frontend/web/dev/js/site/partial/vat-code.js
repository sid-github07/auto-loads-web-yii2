/**
 * Executes anonymous function when document fully loads
 */
$(document).ready(function () {
    registerNaturalVatCodeChange();
    registerLegalVatCodeChange();
});

/**
 * Registers natural VAT code change event
 */
function registerNaturalVatCodeChange() {
    $('.vat-code-item-RG-F-18fd').click(function (e) {
        e.preventDefault();

        /** @member {string} Currently selected VAT code input country code */
        var code = $(this).data('code').toUpperCase();

        changeActiveButton('RG-F-18fd', code);
        changeActiveItem('RG-F-18fd', code);
        changeInputValue('RG-F-18fd', code);
    });
}

/**
 * Registers legal VAT code change event
 */
function registerLegalVatCodeChange() {
    $('.vat-code-item-RG-F-18-je').click(function (e) {
        e.preventDefault();

        /** @member {string} Currently selected VAT code input country code */
        var code = $(this).data('code').toUpperCase();

        changeActiveButton('RG-F-18-je', code);
        changeActiveItem('RG-F-18-je', code);
        changeInputValue('RG-F-18-je', code);
    });
}

/**
 * Changes active VAT code item to newly selected
 *
 * @param {string} id VAT code input ID
 * @param {string} code Newly selected VAT code country code
 */
function changeActiveItem(id, code) {
    $('.vat-code-list-' + id + ' li.active').removeClass('active');
    $('.vat-code-list-' + id + ' li.vat-code-item-' + id + '[data-code="' + code.toLowerCase() + '"]').addClass('active');
}

/**
 * Changes currently active VAT code button value to newly selected
 *
 * @param {string} id VAT code input ID
 * @param {string} code Newly selected VAT code country code
 */
function changeActiveButton(id, code) {
    var currentCode = getActiveCountry(id);
    var removeClass = 'flag-icon-' + currentCode.toLowerCase();
    var addClass = 'flag-icon-' + code.toLowerCase();
    $('#vat-code-' + id + ' i.' + removeClass).removeClass(removeClass).addClass(addClass);
}

/**
 * Returns currently active country code
 *
 * @param {string} id VAT code input ID
 * @returns {string}
 */
function getActiveCountry(id) {
    /** @member {object} VAT code currently active list element */
    var country = $('.vat-code-list-' + id + ' li.active').data();
    return country.code.toLowerCase();
}

/**
 * Changes VAT code input value
 *
 * @param {string} id VAT code input ID
 * @param {string} code VAT code input value that needs to be set
 */
function changeInputValue(id, code) {
    $('#' + id).val(code.toUpperCase());
}