/* global ACCOUNT_TYPE_NATURAL, ACCOUNT_TYPE_LEGAL, accountType, companyInfoByVatCode */

/**
 * Shows container for natural account type and hides container for legal account type
 */
function showNaturalContainer() {
    $('.natural-container').removeClass('hidden');
    $('.legal-container').addClass('hidden');
}

/**
 * Shows container for legal account type and hides container for natural account type
 */
function showLegalContainer() {
    $('.legal-container').removeClass('hidden');
    $('.natural-container').addClass('hidden');
}

/**
 * Hides both containers for natural and legal account types
 */
function hideAccountTypeContainers() {
    $('.natural-container').removeClass('hidden').addClass('hidden');
    $('.legal-container').removeClass('hidden').addClass('hidden');
}

/**
 * Shows or hides containers for natural and legal account types
 *
 * @param {string} accountType Current account type
 */
function toggleAccountTypeContainers(accountType) {
    if (accountType != "") {
        switch (accountType) {
            case ACCOUNT_TYPE_NATURAL:
                showNaturalContainer();
                break;
            case ACCOUNT_TYPE_LEGAL:
                showLegalContainer();
                break;
            default:
                hideAccountTypeContainers();
        }
    } else {
        hideAccountTypeContainers();
    }
}

/**
 * Fills address and/or company name inputs with information, got from VAT code
 *
 * @param {string} vatCode Full VAT code
 * @param {string} address Address input ID
 * @param {boolean} isCompany Attribute, whether company name input must be filled
 * @param {string} companyName Company name input ID or empty string, if this input must not be filled
 */
function fillInputs(vatCode, address, isCompany, companyName) {
    if (vatCode == '') {
        return;
    }

    $.post(companyInfoByVatCode, {
        vatCode: vatCode
    }, function (data) {
        /** @member {object} Response object */
        var response = $.parseJSON(data);

        if (response.valid == false) {
            return;
        }

        //noinspection JSUnresolvedVariable
        $(address).val(response.address);
        if (isCompany) {
            //noinspection JSUnresolvedVariable
            $(companyName).val(response.companyName);
        }
    });
}

/**
 * Executes anonymous function when documents fully loads
 */
$(document).ready(function () {

    toggleAccountTypeContainers(accountType);

    /**
     * Toggles account type containers when user selects account type value
     */
    $('.account-type-input').change(function () {
        var selectedAccountType = $(this).val();
        toggleAccountTypeContainers(selectedAccountType);
    });

    /**
     * Fills user address field with information from VAT code
     */
    $('#RG-F-18fd').change(function () {
        /** @member {string} User selected VAT code country short name */
        var countryCode = $('.active-vat-code-RG-F-18fd').text().trim();
        /** @member {string} VAT code value */
        var value = $(this).val();
        /** @member {string} Full VAT code value */
        var vatCode = countryCode + value;

        fillInputs(vatCode, '#RG-F-18fc', false, '');
    });

    /**
     * Fills user address and company name fields with information from VAT code
     */
    $('#RG-F-18-je').change(function () {
        /** @member {string} User selected VAT code country short name */
        var countryCode = $('.active-vat-code-RG-F-18-je').text().trim();
        /** @member {string} VAT code value */
        var value = $(this).val();
        /** @member {string} Full VAT code */
        var vatCode = countryCode + value;

        fillInputs(vatCode, '#RG-F-18-jd', true, '#RG-F-18-jb');
    });
    
    /**
     * Add checked class to rules agreement checkbox
     */
    $('.custom-checkbox').change(function() {
        $(this).toggleClass('checked');
    });
});