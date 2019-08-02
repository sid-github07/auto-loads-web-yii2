/* global defaultAction */

/**
 * Executes anonymous function when documents fully loads
 */
$(document).ready(function () {

    /**
     * Creates new List class, which filters FAQ list on key up
     *
     * @see {@link http://www.listjs.com/docs/options} for more information
     */
    new List('site-help', {
        valueNames: [ 'faq-question', 'faq-answer' ],
        listClass: 'faq-container'
    });

    /**
     * Triggers click event when user clicks on "No" feedback button
     */
    $('.feedback-button-no').click(function () {
        /** @member {string} Current question placeholder */
        var questionPlaceholder = $(this).attr('data-placeholder');
        /** @member {string} Current question ID */
        var questionId = $(this).attr('data-id');
        /** @member {string} Newly formed form action */
        var action = defaultAction + questionPlaceholder;

        $('#faq-feedback-form').attr('action', action);
        $('.feedback-form-container').removeClass('hidden').detach().appendTo('#' + questionId + ' .feedback-buttons');
    });

    /**
     * Adds or removes "expanded" class to/from FAQ-question element when user presses on specific question
     */
    $('.faq-question').click(function () {
        $(this).toggleClass('expanded');
//        $(this).find('.faq-question-icon .icon-plus, .faq-question-icon .icon-minus').toggleClass('hidden');
    });

    /**
     * Removes "expanded" class from FAQ-question element after button "yes" is pressed
     */
    $(".success-btn").click(function() {
        /** @member {string} Current button data-target attribute value*/
        var dataTarget = $(this).data('target');
        $('.faq-question[data-target="' + dataTarget + '"]').toggleClass('expanded');
    });
});