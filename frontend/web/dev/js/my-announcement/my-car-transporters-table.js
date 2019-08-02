'use strict';

/* global
actionChangeCarTransportersPageSize,
actionChangeCarTransporterAvailableFromDate,
VISIBLE,
INVISIBLE,
actionChangeCarTransportersVisibility,
actionRemoveCarTransporters
*/

/**
 * Changes specific car transporter available from date
 *
 * @param {number} id Specific car transporter ID whose available from date needs to be changed
 */
function changeCarTransporterAvailableFromDate(id) {
    $('#C-T-52_' + id).change(function () {
        var availableFromDate = $(this).val();

        $.pjax({
            type: 'POST',
            url: appendUrlParams(actionChangeCarTransporterAvailableFromDate),
            data: {
                id: id,
                availableFromDate: availableFromDate
            },
            container: '#my-car-transporters-table-pjax',
            push: false,
            replace: false,
            scrollTo: false
        }).done(function () {
            $.pjax.reload({ container: '#toastr-pjax' });
        });
    });
}

/**
 * Changes visibility of car transporters
 *
 * @param {number} element changed loads view status
 */
function changeCarTransporterTypeShowing(element) {
    updateUrlParam('car-transporter-activity', element);
    updateUrlParam('car-transporter-page', 1);
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeCarTransporterTableFiltration),
        container: '#my-car-transporters-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $.pjax.reload({ container: '#toastr-pjax' });
    });
}

function editQuantity(e, rowId) {
    e.stopPropagation();
    $('#w' + rowId).editable('toggle');
}

/**
 * Renders Transporter advert form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderAdvertizeTransportForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionTransporterAdvForm),
        data: { id: id },
        container: '#adv-transporter-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#adv-transporter-modal').modal('show');
    });
}

/**
 * Renders Transporter open contacts form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderTransporterOpenContactsForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionTransporterOpenContactsForm),
        data: { id: id },
        container: '#transporter-open-contacts-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#transporter-open-contacts-modal').modal('show');
    });
}

/**
 * Renders car transporter preview form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderTransporterPreviewForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionPreviewTransporter),
        data: { transporterId: id },
        container: '#transporter-preview-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#transporter-preview-modal').modal('show');
    });
}

/**
 * Makes multiple or specific car transporter visible
 *
 * @param {object} e Event object
 * @param {number|null|object} id Specific car transporter ID that needs to be made visible
 */
function makeCarTransporterVisible(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-car-transporters-grid-view').yiiGridView('getSelectedRows');
    }

    if ($.isNumeric(id) || !$.isEmptyObject(id)) {
        return changeCarTransporterVisibility(id, VISIBLE);
    }
}

/**
 * Makes multiple or specific car transporter invisible
 *
 * @param {object} e Event object
 * @param {number|null|object} id Specific car transporter ID that needs to be made invisible
 */
function makeCarTransporterInvisible(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-car-transporters-grid-view').yiiGridView('getSelectedRows');
    }

    if ($.isNumeric(id) || !$.isEmptyObject(id)) {
        return changeCarTransporterVisibility(id, INVISIBLE);
    }
}

/**
 * Changes multiple or specific car transporter visibility
 *
 * @param {number|array} id List of car transporters IDs or concrete car transporter ID that visibility needs to be changed
 * @param {number} visibility New car transporter visibility
 */
function changeCarTransporterVisibility(id, visibility) {
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeCarTransportersVisibility),
        data: {
            id: id,
            visibility: visibility
        },
        container: '#my-car-transporters-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $.pjax.reload({ container: '#toastr-pjax' });
    });
}

/**
 * Removes multiple or specific car transporter
 *
 * @param {object} e Event object
 * @param {number|null|jQuery} id List of car transporters IDs or concrete car transporter ID that needs to be removed
 */
function removeCarTransporters(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-car-transporters-grid-view').yiiGridView('getSelectedRows');
    }

    if (!$.isNumeric(id) && $.isEmptyObject(id)) {
        return;
    }

    $('#remove-car-transporter-button-yes').unbind('click').bind('click', function () {
        $.pjax({
            type: 'POST',
            url: appendUrlParams(actionRemoveCarTransporters),
            data: { id: id },
            container: '#my-car-transporters-table-pjax',
            push: false,
            replace: false,
            scrollTo: false
        }).done(function () {
            $('#remove-car-transporter-modal').modal('hide');
            $.pjax.reload({ container: '#toastr-pjax' });
        });
    });

    $('#remove-car-transporter-modal').modal('show');
}

/**
 * Changes car transporters table page size
 *
 * @param {object} element This object
 */
function changeCarTransporterPageSize(element) {
    var pageSize = $(element).val();

    updateUrlParam('car-transporter-page', 1);
    updateUrlParam('car-transporter-per-page', pageSize);

    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeCarTransportersPageSize),
        container: '#my-car-transporters-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('.' + $(element).attr('class')).val(pageSize);
    });
}

/**
 * Changes company loads visible load in one page number
 */
function changeLoadPageNumber(e, element) {
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
