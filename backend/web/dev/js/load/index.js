/* global actionPreviewsLoads, actionLoadPreview, actionCompanyLoads */

/**
 * Renders load previews
 *
 * @param {object} e Event object
 * @param {number} loadId Load ID
 */
function previews(e, loadId) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionPreviewsLoads,
        data: {
            id: loadId
        },
        container: '#loads-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showPreviews();
    });
}

/**
 * Shows load previews modal
 */
function showPreviews() {
    $('#previews-modal').modal('show');
}

/**
 * Changes company loads visible load in one page number
 */
function changeLoadPageNumber(e, element) {
    var pageNumber = $('#A-C-325').val();
    updateParams('load-page', 1);
    updateParams('per-load-page', pageNumber);
    $.pjax({
        type: 'POST',
        url: window.location.href,
        container: '#pjax-load-container',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        $('#' + $(element).attr('id')).val(pageNumber);
    });
}

function updateParams(param, size) {
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam(param, size, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
}

/**
 * Shows or hides load preview
 *
 * @param {object} e Event object
 * @param {number} loadId Load ID
 */
function loadPreview(e, loadId) {
    e.preventDefault();
    if (isPreviewClosed(loadId)) {
        renderLoadPreview(loadId);
    } else {
        hideLoadPreviewRow(loadId);
    }
}

/**
 * Checks whether current load preview is not opened
 *
 * @param {number} loadId Load ID
 * @returns {boolean}
 */
function isPreviewClosed(loadId) {
    return $('tr.load-preview-row td#' + loadId).parent().hasClass('hidden');
}

/**
 * Renders load preview
 *
 * @param {number} loadId Load ID
 */
function renderLoadPreview(loadId) {
    $.post(actionLoadPreview, {id: loadId}, function (response) {
        if (!response) {
            // NOTE: checks if response is not an empty string
            $.pjax.reload({container: '#toastr-pjax', timeout:2e3});
        } else {
            addLoadPreviewContent(loadId, response);
            showLoadPreviewRow(loadId);
        }
    });
}

/**
 * Adds rendered load information to load preview place
 *
 * @param {number} loadId Load ID
 * @param {string} content Rendered information about load
 */
function addLoadPreviewContent(loadId, content) {
    $('tr.load-preview-row td#' + loadId).html(content);
}

/**
 * Shows load preview row
 *
 * @param {number} loadId Load ID
 */
function showLoadPreviewRow(loadId) {
    $('tr.load-preview-row td#' + loadId).parent().removeClass('hidden');
}

/**
 * Hides load preview row
 *
 * @param {number} loadId Load ID
 */
function hideLoadPreviewRow(loadId) {
    $('tr.load-preview-row td#' + loadId).parent().addClass('hidden');
}

/**
 * Renders load advert form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderAdvertizeForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionLoadAdvForm,
        data: { id: id },
        container: '#adv-load-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#adv-load-modal').modal('show');
    });
}

/**
 * Renders load open contacts form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderOpenContactsForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionLoadOpenContactsForm,
        data: { id: id },
        container: '#open-contacts-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#open-contacts-modal').modal('show');
    });
}

$(document).ready(function($){
    $('.advert-tag').on('click', function(evt) {
        var url = $(this).parent().find('.adv-url');
        if (url) {
            url.trigger('click');
        }
    });
    $('.visible-tag').on('click', function(evt) {
        var url = $(this).parent().find('.visible-url');
        if (url) {
            url.trigger('click');
        }
    });
});