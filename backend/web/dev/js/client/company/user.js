/* global actionCompanyUserEditForm, actionCompanyUserAddForm, actionCompanyUserActivityPreview, actionValidateVatCode */

/**
 * Renders user information editing form in modal
 *
 * @param {object} e Event object
 * @param {number} id User ID
 */
function editCompanyUser(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionCompanyUserEditForm,
        data: {
            id: id
        },
        container: '#edit-company-user-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showCompanyUserEditingModal();
    });
}

/**
 * Shows user editing modal
 */
function showCompanyUserEditingModal() {
    $('#edit-company-user-modal').modal('show');
}

/**
 * Renders company user adding form in modal
 *
 * @param {object} e Event object
 * @param {number} id Company ID
 */
function addCompanyUser(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionCompanyUserAddForm,
        data: {
            id: id
        },
        container: '#add-company-user-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showCompanyUserAddingModal();
    });
}

/**
 * Shows company user adding modal
 */
function showCompanyUserAddingModal() {
    $('#add-company-user-modal').modal('show');
}

/**
 * Toggles checkbox element
 *
 * @param {object} selector This object
 */
function toggleCheckbox(selector) {
    if ($(selector).is(':checked')) {
        $(selector).parent().addClass('checked');
    } else {
        $(selector).parent().removeClass('checked');
    }
}

/**
 * Renders company user activity preview modal
 *
 * @param {object} e Event object
 * @param {number} id User ID
 */
function previewUserActivity(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionCompanyUserActivityPreview,
        data: {
            id: id
        },
        container: '#preview-company-user-activity-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showCompanyUserActivityPreviewModal();
    });
}

/**
 * Shows company user activity preview modal
 */
function showCompanyUserActivityPreviewModal() {
    $('#preview-company-user-activity-modal').modal('show');
}

/**
 * Validates company VAT code
 */
function validateVatCode() {
    var code = $('.vat-code-input').val();
    $.post(actionValidateVatCode, {code: code}, function (response) {
        $('.validate-vat-code-button').attr('data-content', response);
    }).done(function () {
        $('.validate-vat-code-button').popover('show');
    });
}