/**
 * Executes anonymous function when documents fully loads
 */
$(document).ready(function () {
    /**
     * Add checked class to rules agreement checkbox
     */
    $('.custom-checkbox').change(function() {
        $(this).toggleClass('checked');
    });
});
