/**
 * Executes anonymous function when document fully loads
 */
$(document).on('ready pjax:success', function() {

    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });

    checkedCheckbox();
    $('.custom-checkbox > input').click(function() {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
});

/**
 * Toggles class if checkbox is checked
 */
function checkedCheckbox() {
    $('.custom-checkbox > input').each(function() {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
}