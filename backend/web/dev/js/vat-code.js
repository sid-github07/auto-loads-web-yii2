/**
 * Returns active VAT code country in lowercase
 *
 * Active VAT code country is country, that was selected by default, or selected by user before
 *
 * @param {string} type Account type. Must be "natural" or "legal"
 * @returns {string}
 */
function getVatCodeActiveCountry(type) {
    /** @member {object} Active country object. Consists only of "countryCode" */
    var activeCountry = $('.vat-code-' + type + '-selection-list').find('li.active').data();
    //noinspection JSUnresolvedVariable
    return activeCountry.countryCode.toLowerCase();
}

/**
 * Removes active class from currently active VAT code country list item
 * and adds that class to currently selected VAT code country list item
 *
 * @param {string} type Account type. Must be "natural" or "legal"
 * @param selectedCode Currently selected VAT code country in uppercase
 */
function changeVatCodeActiveItem(selectedCode, type) {
    /** @member {object} Selector for VAT code selection list */
    var vatCodeSelectionList = $('.vat-code-' + type + '-selection-list');

    vatCodeSelectionList.find('li.active').removeClass('active');
    vatCodeSelectionList.find("li.vat-code-" + type + "-item[data-country-code='" + selectedCode + "']").addClass('active');
}

/**
 * Replaces active country code flag and country code to currently selected country flag and country code
 *
 * Active VAT code country is country, that was selected by default, or selected by user before.
 * Currently selected country is that that user currently selected from VAT code country list
 * and needs to be set as active VAT code country
 *
 * @param {string} currentCountryCode Country code in lowercase that was active, before user selection
 * @param {string} selectedCountryCode Currently selected VAT code country in uppercase
 * @param {string} type Account type. Must be "natural" or "legal"
 */
function changeSelectedVatCodeItem(currentCountryCode, selectedCountryCode, type) {
    /** @member {string} Active country flag class that needs to be removed */
    var classToRemove = 'flag-icon-' + currentCountryCode;
    /** @member {string} Currently selected country flag class that needs to be set */
    var classToAdd = 'flag-icon-' + selectedCountryCode.toLowerCase();
    /** @member {object} Selector for VAT code selection */
    var vatCodeSelection = $('#vat-code-' + type + '-selection');

    vatCodeSelection.find('.flag-icon-' + currentCountryCode).removeClass(classToRemove).addClass(classToAdd);
    vatCodeSelection.find('.country-code').text(selectedCountryCode);
}

/**
 * Executes anonymous function when user selects specific country for VAT code from drop down list menu
 */
$(document).ready(function () {
    /**
     * Executes anonymous function when user clicks on specific VAT code when account type selected as natural
     */
    $('.vat-code-natural-item, .vat-code-legal-item').click(function (e) {
        e.preventDefault(); /* NOTE: prevents from scrolling to top when clicked on country item */
        /** @member {string} Currently selected country for VAT code */
        var selectedCode = $(this).data('country-code').toUpperCase();
        /** @member {string} User account type. Must be "natural" or "legal" */
        var type = $(this).data('account-type');

        /** @member {string} Country, that was selected by default, or selected by user before */
        var activeCountryCode = getVatCodeActiveCountry(type);
        changeVatCodeActiveItem(selectedCode, type);
        changeSelectedVatCodeItem(activeCountryCode, selectedCode, type);

        /** NOTE: VAT code input filled with currently selected VAT code country short code */
        $('.vat-code-' + type + '-input').val(selectedCode);
    });
});