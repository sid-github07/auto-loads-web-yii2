/* global actionChangeCompanyArchivation, actionRenderCompanyNameChangeForm, NOT_ARCHIVED, ARCHIVED */

/**
 * Executes various functions when document fully loads
 */
$(document).ready(function () {
    revertCompanyArchivation();
    registerVatCodeChanges();
});

/**
 * Triggers company archivation event
 *
 * @param {object} select Company archivation select object
 */
function triggerCompanyArchivation(select) {
    var value = select.value;
    $('#change-company-archivation-yes').attr('data-archived', value);
    showCompanyArchivationModal();
}

/**
 * Shows company archivation modal
 */
function showCompanyArchivationModal() {
    $('#change-company-archivation').modal('show');
}

/**
 * Changes company archivation
 *
 * @param {object} button Company archivation modal yes button object
 */
function changeCompanyArchivation(button) {
    var value = $(button).attr('data-archived');
    $.post(actionChangeCompanyArchivation, {archive: value}, function () {
        hideCompanyArchivationModal();
    });
}

/**
 * Hides company archivation modal
 */
function hideCompanyArchivationModal() {
    $('#change-company-archivation').modal('hide');
}

/**
 * Reverts company archivation event to previous stage
 */
function revertCompanyArchivation() {
    $('#change-company-archivation').on('hidden.bs.modal', function () {
        var value = $('#A-M-8').val() == NOT_ARCHIVED ? ARCHIVED : NOT_ARCHIVED;
        $('#A-M-8').val(value);
    });
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
 * Fills company address and company name fields with information from VAT code
 */
function registerVatCodeChanges() {
    $('#A-C-69').change(function () {
        /** @member {string} User selected VAT code country short name */
        var countryCode = $('.active-vat-code-A-C-69').text().trim();
        /** @member {string} VAT code value */
        var value = $(this).val();
        /** @member {string} Full VAT code */
        var vatCode = countryCode + value;

        fillInputs(vatCode, '#A-C-75', true);
    });
}

/**
 * Changes company name
 *
 * @param {object} e Event object
 */
function changeCompanyName(e) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderCompanyNameChangeForm,
        container: '#change-company-name-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showChangeCompanyNameModal();
    });
}

/**
 * Shows company name change modal
 */
function showChangeCompanyNameModal() {
    $('#change-company-name-modal').modal('show');
}