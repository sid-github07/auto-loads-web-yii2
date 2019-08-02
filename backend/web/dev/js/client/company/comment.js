/* global actionRenderCompanyCommentForm, COMMENT_MAX_LENGTH */

/**
 * Adds comment to specific company
 *
 * @param {object} e Event object
 * @param {number} id Company ID
 * @param {number} toIndex Attribute, whether admin must be redirected to index after form submit
 */
function addComment(e, id, toIndex) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderCompanyCommentForm,
        data: {
            id: id,
            toIndex: toIndex
        },
        container: '#company-comment-pjax-' + id,
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Displays current company comment number of characters
 *
 * @param {object} selector This object
 * @param {number} id Company ID
 */
function showCommentLength(selector, id) {
    var text = $(selector).val();
    var length = text.length;
    updateDisplayedTextLength(id, length);
    toggleMaxTextLengthClass(id, length);
}

/**
 * Updates currently displayed company's comment number of characters
 *
 * @param {number} id Company ID
 * @param {number} length Company's comment length
 */
function updateDisplayedTextLength(id, length) {
    $('.comment-length-' + id).text(length);
}

/**
 * Toggles exceeded maximum length class
 *
 * @param {number} id Company ID
 * @param {number} length Company's comment length
 */
function toggleMaxTextLengthClass(id, length) {
    if (length > COMMENT_MAX_LENGTH) {
        $('.comment-length-container-' + id).addClass('exceeded-text-length');
    } else {
        $('.comment-length-container-' + id).removeClass('exceeded-text-length');
    }
}

/**
 * Removes company comment
 *
 * @param {object} e Event object
 * @param {string} link Link to remove company comment action
 */
function removeComment(e, link) {
    e.preventDefault();
    changeRemoveCommentButtonLink(link);
    showCommentRemovingModal();
}

/**
 * Changes remove comment button link
 *
 * @param {string} link Link to remove company comment action
 */
function changeRemoveCommentButtonLink(link) {
    $('#remove-comment-yes').attr('href', link);
}

/**
 * Shows comment removing modal
 */
function showCommentRemovingModal() {
    $('#remove-comment-modal').modal('show');
}