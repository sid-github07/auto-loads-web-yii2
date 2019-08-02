/**
 * Initializes tooltip
 */
$('[data-toggle="tooltip"]').tooltip({
    trigger: 'hover'
});

/**
 * Clears created_at field value
 */
function clearCreatedAt() {
    console.log('CREATED_AT');
    $('.created_at').val('');
}

/**
 * Clears period field value
 */
function clearPeriod() {
    console.log('PERIOD');
    $('.period').val('');
}

/**
 * Clears data range values
 */
function clearDateRange() {
    console.log('DATE_RANGE');
    $('.dateFrom').val('');
    $('.dateTo').val('');
}

/**
 * Clear end date field value
 */
function clearEndDate() {
    console.log('END_DATE');
    $('.end_date').val('');
}

$('#export-xml').click(function () {
    var queryString = $('#bill-list-filtration-form').serialize();
    var actionUrl = $(this).attr('data-url') + '?' + queryString;
    window.location = actionUrl;
});