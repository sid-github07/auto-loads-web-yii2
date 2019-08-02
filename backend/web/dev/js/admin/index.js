/* global actionRenderEditForm, actionRenderChangePasswordForm, actionRemove */

/**
 * Renders administrator/moderator information edit form
 *
 * @param {object} e Event object
 * @param {numeric} id Admin ID
 */
function edit(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderEditForm,
        data: {id: id},
        container: '#edit-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showEditModal();
    });
}

/**
 * Shows administrator/moderator information edit modal
 */
function showEditModal() {
    $('#edit-modal').modal('show');
}

/**
 * Renders administrator/moderator password change form
 *
 * @param {object} e Event object
 * @param {numeric} id Admin ID
 */
function changePassword(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderChangePasswordForm,
        data: {id: id},
        container: '#change-password-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showChangePasswordModal();
    });
}

/**
 * Shows administrator/moderator password change modal
 */
function showChangePasswordModal() {
    $('#change-password-modal').modal('show');
}

/**
 * Shows administrator/moderator remove modal
 *
 * @param {object} e Event object
 * @param {numeric} id Admin ID
 */
function showRemoveModal(e, id) {
    e.preventDefault();
    $('#delete-admin-button-yes').attr('data-id', id);
    $('#remove-modal').modal('show');
}

/**
 * Removes administrator/moderator
 */
function remove() {
    var id = $('#delete-admin-button-yes').attr('data-id');
    $.post(actionRemove, {id: id});
}