/* global actionIndex, actionCompanyUserEditForm, actionLoadUserRoutes */

/**
 * Executes anonymous function when document fully loads
 */
$(document).ready(function() {
    registerEmailSearchButton();
});

$(document).on('pjax:end', '#users-email-pjax', function () {
   registerEmailSearchButton();
});

/**
 * Registers edit load icon event
 */
function registerEmailSearchButton() {
    $('#A-C-8').unbind('click').bind('click', function (e) {
        e.preventDefault();
        renderEmailModal();
    });
}

/**
 * Renders edit load to edit load modal
 */
function renderEmailModal() {
    showEditLoadModal();
}

/**
 * Shows edit load modal
 */
function showEditLoadModal() {
    $('#company-email-modal').modal('show');
}

/**
 * Renders company owner/user information editing form in modal
 *
 * @param {object} e Event object
 * @param {number} id Company owner/user ID
 */
function editUser(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionCompanyUserEditForm,
        data: {
            id: id
        },
        container: '#edit-user-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showUserEditingModal();
    });
}

/**
 * Open modal with user routes
 * @param {object} e
 * @param {number} user_id
 */
function loadUserRoutes(e, user_id)
{
  e.preventDefault();
  $.pjax({
    type: 'POST',
    url: actionLoadUserRoutes,
    data: {
      user_id: user_id
    },
    container: '#user-routes-pjax',
    push: false,
    scrollTo: false,
    cache: false
  }).done(function () {
    $('#user-routes-modal').modal('show');
  });
}

/**
 * Shows company owner/user editing modal
 */
function showUserEditingModal() {
    $('#edit-user-modal').modal('show');
}

/**
 * Changes potentiality
 */
function changeCompanyPotentiality(companyId) {
    var status = $('#potential-' + companyId).is(':checked');
    $.post(actionCheckPotential, { companyId: companyId, status: status }, function () {});
}