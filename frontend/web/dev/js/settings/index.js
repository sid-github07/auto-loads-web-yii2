/** @member {object} Container for documents */
var documents = {};

/**
 * Shows change email modal on change email button click
 */
function showChangeEmailModal() {
    $('.change-email').click(function () {
        $('#change-email').modal('show');
    });
}

/**
 * Shows change VAT code modal on change VAT code button click
 */
function showChangeVatCodeModal() {
    $('.change-vat-code').click(function () {
        $('#change-vat-code').modal('show');
    });
}

/**
 * Returns document type
 *
 * @param {object} element Current document element
 * @returns {*}
 */
function getDocumentType(element) {
    return element.closest('.document-pjax').data('document-type');
}

/**
 * Returns document PJAX container ID
 *
 * @param {object} element Current document element
 * @returns {*}
 */
function getPjaxContainerId(element) {
    return element.closest('.document-pjax').attr('id');
}

/**
 * Adds selected document to document container
 */
function addDocument() {
    $('.document-file').change(function (event) {
        /** @member {string} Document type */
        var type = getDocumentType($(this));
        /** @member {string} Document upload input name */
        var name = $(this).attr('name');
        documents[type] = {
            file: event.target.files,
            name: name
        };
    });
}

/**
 * Returns upload document form action
 *
 * @param {string} type Document type
 * @param {string} container PJAX container ID
 * @returns {*|jQuery}
 */
function getUploadAction(type, container) {
    /** @member {string} Document date of expiry */
    var date = $('#' + container + '[data-document-type="' + type + '"] .document-date').val();
    /** @member {string} Document upload form action */
    var action = $('#' + container + '[data-document-type="' + type + '"] .document-form').attr('action');
    if (date.length > 0) {
        action += '/' + date;
    }
    return action;
}

/**
 * Returns document data, ready for POST
 *
 * @param {string} type Document type
 * @returns {*}
 */
function getDocument(type) {
    /** @member {object} Document data container */
    var data = new FormData();
    $.each(documents[type].file, function (key, value) {
        data.append(documents[type].name, value);
    });
    return data;
}

/**
 * Uploads document
 *
 * @param {string} type Document type
 * @param {string} container PJAX container ID
 */
function uploadDocument(type, container) {
    $.pjax({
        type: 'POST',
        url: getUploadAction(type, container),
        data: getDocument(type),
        container: '#' + container,
        push: false,
        scrollTo: false,
        cache: false,
        processData: false, // Don't process the files
        contentType: false // jQuery will tell the server its a query string request
    });
}

/**
 * Executes anonymous function on document submit button click
 */
function submitForm() {
    $('.document-submit').click(function () {
        /** @member {string} Document type */
        var type = getDocumentType($(this));
        /** @member {string} PJAX container ID */
        var container = getPjaxContainerId($(this));
        uploadDocument(type, container);
    });
}

/**
 * Removes document
 *
 * @param {string} url URL to document remove action
 * @param {string} container PJAX container ID
 */
function removeDocument(url, container) {
    $.pjax({
        type: 'POST',
        url: url,
        container: '#' + container,
        push: false,
        scrollTo: false
    });
}

/**
 * Executes anonymous function on document remove button click
 */
function deleteDocument() {
    $('.document-remove').click(function (event) {
        event.preventDefault();
        /** @member {string} URL to document remove action */
        var url = $(this).attr('href');
        /** @member {string} PJAX container ID */
        var container = getPjaxContainerId($(this));
        removeDocument(url, container);
    });
}

/**
 * Executes anonymous function on document update button click
 */
function showDocumentForm() {
    $('.document-update').click(function () {
        /** @member {string} Document type */
        var type = getDocumentType($(this));
        /** @member {string} PJAX container ID */
        var container = getPjaxContainerId($(this));
        $('#' + container + '[data-document-type="' + type + '"] .document-form-container').removeClass('hidden');
    });
}

/**
 * Executes anonymous function on document form clock button click
 */
function hideDocumentForm() {
    $('.document-form-close').click(function () {
        /** @member {string} Document type */
        var type = getDocumentType($(this));
        /** @member {string} PJAX container ID */
        var container = getPjaxContainerId($(this));
        $('#' + container + '[data-document-type="' + type + '"] .document-form-container').addClass('hidden');
    });
}

/**
 * Executes anonymous function when documents fully loads
 */
$(document).on('ready pjax:end', function () {
    
    /** Shows change email modal on change email button click */
    showChangeEmailModal();

    /** Shows change VAT code modal on change VAT code button click */
    showChangeVatCodeModal();

    /** Adds selected document to document container */
    addDocument();

    /** Executes anonymous function on document submit button click */
    submitForm();

    /** Executes anonymous function on document remove button click */
    deleteDocument();

    /** Executes anonymous function on document update button click */
    showDocumentForm();

    /** Executes anonymous function on document form clock button click */
    hideDocumentForm();
});

/**
 * Executes anonymous function on successful specific PJAX containers update
 */
$(document).on('pjax:success', '#document-cmr, #document-eu, #document-im', function () {
    $.pjax.reload({
        container: '#document-toastr',
        timeout: 2e3
    });
});