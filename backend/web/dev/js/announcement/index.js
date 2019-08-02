'use strict';

/**
 *
 * @param e
 * @param id
 */
function edit(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderEditForm,
        data: { id: id },
        container: '#edit-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showEditModal();
    });
}

/**
 * Shows information edit modal
 */
function showEditModal() {
    $('#edit-modal').modal('show');
}

/**
 * Shows remove modal
 *
 * @param {object} e Event object
 * @param {numeric} id Admin ID
 */
function showRemoveModal(e, id) {
    e.preventDefault();
    $('#delete-announcement-button-yes').attr('data-id', id);
    $('#remove-modal').modal('show');
}

/**
 * Shows remove modal
 *
 * @param {object} e Event object
 * @param {numeric} id Admin ID
 */
function showHideModal(e, id) {
    e.preventDefault();
    $('#hide-announcement-button-yes').attr('data-id', id);
    $('#hide-modal').modal('show');
}
/**
 * Removes announcement
 */
function remove() {
    var id = $('#delete-announcement-button-yes').attr('data-id');
    $.post(actionRemove, { id: id });
}

/**
 * sets announcement to hidden
 */
function hide() {
    var id = $('#hide-announcement-button-yes').attr('data-id');
    $.post(actionHide, { id: id });
}