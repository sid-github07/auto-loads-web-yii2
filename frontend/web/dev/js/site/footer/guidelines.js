/**
 * Executes anonymous function when documents fully loads
 */
$(document).ready(function () {
    /**
     * Adds expanded class when guidelines question link is clicked
     */
    $('.guidelines-question').click(function() {
        $(this).toggleClass('expanded');
    });
});