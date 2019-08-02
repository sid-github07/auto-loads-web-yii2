/* global actionRenderEditForm,  */

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

function showEditModal() {
    $('#edit-modal').modal('show');
}
