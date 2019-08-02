/* global actionPreviews, actionContactInfoPreview */

/**
 * Renders car transporter previews
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function showCarTransporterPreview(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionPreviews,
        data: {id: id},
        container: '#car-transporter-previews-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#car-transporter-previews-modal').modal('show');
    });
}

/**
 * Shows or hides car transporter preview
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function collapseCarTransporterPreview(e, id) {
    e.preventDefault();

    var td = $('tr.car-transporter-preview-row td#' + id);
    var tr = td.parent();

    if (td.text().length == 0) {
        $.post(actionContactInfoPreview, {id: id}, function (content) {
            td.html(content);
            tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
        });
    } else {
        tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
    }
}

/**
 * Changes company car transporters visible car transporter in one page number
 */
function changeCarTransporterPageNumber(e, element) {
    var pageNumber = $('#C-T-105').val();
    updateParams('car-transporter-page', 1);
    updateParams('car-transporter-per-page', pageNumber);
    $.pjax({
        type: 'POST',
        url: window.location.href,
        container: '#car-transporter-list-pjax',
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
 * Renders car transporter open contacts form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderOpenContactsForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionTransporterOpenContactsForm,
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