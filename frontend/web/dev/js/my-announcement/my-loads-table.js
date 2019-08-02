/* global
actionChangeLoadDate,
actionLoadEditingForm,
actionChangeLoadsVisibility,
actionRemoveLoads,
actionChangeLoadsPageSize,
ACTIVATED,
NOT_ACTIVATED
*/

/**
 * Binds tooltip on each successful PJAX request
 */
$(document).on('pjax:success', function() {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });
});

/**
 * Changes load date
 *
 * @param {number} id Specific load ID whose date needs to be changed
 */
function changeLoadDate(id) {
    $('#MK-C-15_' + id).change(function () {
        var date = $(this).val();
        $.pjax({
            type: 'POST',
            url: appendUrlParams(actionChangeLoadDate),
            data: {
                id: id,
                date: date
            },
            container: '#my-loads-table-pjax',
            push: false,
            replace: false,
            scrollTo: false
        }).done(function () {
            $.pjax.reload({container: '#toastr-pjax'});
        });
    });
}

/**
 * Renders load editing form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderLoadEditingForm(e, id) {
    e.preventDefault();
    $('#load-announcement-form').html("");
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionLoadEditingForm),
        data: {id: id},
        container: '#edit-load-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#edit-load-modal').modal('show');
        $('.load_cars_editing_container').on('afterInsert', function () {
            toggleRemoveButtonsVisibility();
            toggleRequiredClass();
            togglePriceVisibility();
            toggleQuantityVisibility();
            validateQuantity();
            toggleEditableLoadElementsStructure();
        }).on('afterDelete', function () {
            toggleRemoveButtonsVisibility();
            toggleQuantityVisibility();
            validateQuantity();
            toggleEditableLoadElementsStructure();
        });
    });
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
        url: appendUrlParams(actionLoadAdvForm),
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
 * Renders open contacts form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderLoadOpenContactsForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionLoadOpenContactsForm),
        data: {id: id},
        container: '#load-open-contacts-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#load-open-contacts-modal').modal('show');
    });
}

/**
 * @param {object} e Event object
 * @param {string} service service name
 * @param {number} loadId Load ID
 * @param {number} userId User ID
 */
var processed = {};
function getUserContacts(e, service, loadId, userId) {
    console.log(1);
    e.preventDefault();
    if (processed[service + loadId + userId] === 1) {
        return;
    }
    processed[service + loadId + userId] = 1;

    var table = $('#' + service);
    var alert = $('#alert-' + service);
    if (table.length) {
        var trParent = table.find('tr[data-user=' + userId + ']');
        var tr = table.find('tr[data-user-contacts=' + userId + ']');
        if (tr.length) {
            var td = tr.children('td');
            if (!td.contents().length) {
                $.post('/my-load/' + service, {loadId: loadId, userId: userId, action: 'get-contacts'}, function (response) {
                    if (response.error !== undefined && response.error !== null) {
                      td.html(response.error);
                      tr.show();
                    }
                    if (response.alert !== undefined && response.content !== undefined && response.params !== undefined) {
                        trParent.find('td.viewed').text(response.params['viewed']);
                        trParent.find('td.marker').text('');
                        processed[service + loadId + userId] = 0;
                        td.html(response.content);
                        if (alert.length) {
                          alert.html(response.alert);
                        }
                        trParent.css('background-color', '#fff');
                        tr.show();
                    }
                });
            } else {
                processed[service + loadId + userId] = 0;
                tr.toggle();
            }
        }
    }
}


/**
 * Renders load advert form
 *
 * @param {object} e Event object
 * @param {number} id Specific load ID that edit for needs to be rendered
 */
function renderPreviewForm(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionPreviewLoad),
        data: { loadId: id },
        container: '#load-preview-modal-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#load-preview-modal').modal('show');
    });
}

function renderCustomLoadForm(id, url, form) {
    $.pjax({
        type: 'POST',
        url: appendUrlParams(url),
        data: { loadId: id },
        container: '#' + form + '-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#' + form).modal('show');
    });
}

/**
 * Makes multiple or specific load visible
 *
 * @param {object} e Event object
 * @param {number|null|object} id Specific load ID that needs to be made visible
 */
function makeLoadsVisible(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-loads-grid-view').yiiGridView('getSelectedRows');
    }

    if ($.isNumeric(id) || !$.isEmptyObject(id)) {
        return changeLoadsVisibility(id, ACTIVATED);
    }
}

/**
 * Makes multiple or specific load invisible
 *
 * @param {object} e Event object
 * @param {number|null|object} id Specific load ID that needs to be made invisible
 */
function makeLoadsInvisible(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-loads-grid-view').yiiGridView('getSelectedRows');
    }

    if ($.isNumeric(id) || !$.isEmptyObject(id)) {
        return changeLoadsVisibility(id, NOT_ACTIVATED);
    }
}

/**
 * Changes visibility of loads
 *
 * @param {number} element changed loads view status
 */
function changeLoadsTypeShowing(element) {
    updateUrlParam('load-activity', element);
    updateUrlParam('load-page', 1);
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeLoadTableFiltration),
        container: '#my-loads-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $.pjax.reload({container: '#toastr-pjax'});
    });
}

/**
 * Changes multiple or specific load visibility
 *
 * @param {number|array} id List of loads IDs or concrete load ID that status needs to be changed
 * @param {number} newStatus New loads status
 */
function changeLoadsVisibility(id, newStatus) {
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeLoadsVisibility),
        data: {
            id: id,
            newStatus: newStatus
        },
        container: '#my-loads-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $.pjax.reload({container: '#toastr-pjax'});
    });
}

/**
 * Removes multiple or specific load
 *
 * @param {object} e Event object
 * @param {number|null} id List of loads IDs or concrete load ID that needs to be deleted
 */
function removeLoads(e, id) {
    e.preventDefault();

    if (id == null) {
        id = $('#my-loads-grid-view').yiiGridView('getSelectedRows');
    }

    if (!$.isNumeric(id) && $.isEmptyObject(id)) {
        return;
    }

    $('#remove-load-button-yes').unbind('click').bind('click', function () {
        $.pjax({
            type: 'POST',
            url: appendUrlParams(actionRemoveLoads),
            data: {id: id},
            container: '#my-loads-table-pjax',
            push: false,
            replace: false,
            scrollTo: false
        }).done(function () {
            $('#remove-load-modal').modal('hide');
            $.pjax.reload({container: '#toastr-pjax'});
        });
    });

    $('#remove-load-modal').modal('show');
}

/**
 * Changes loads table page size
 *
 * @param {object} element This object
 */
function changeLoadPageSize(element) {
    var pageSize = $(element).val();
    updateUrlParam('load-page', 1);
    updateUrlParam('load-per-page', pageSize);

    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionChangeLoadsPageSize),
        container: '#my-loads-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('.' + $(element).attr('class')).val(pageSize);
    });
}

$(document).click(function (e) {
    if ($(e.target).is('.simple-dropdown') || $(e.target).parents('.simple-dropdown').length) {
      $(e.target).parents('.simple-dropdown').find('ul').toggle();
    } else {
      $('.simple-dropdown').find('ul').hide();
    }
});