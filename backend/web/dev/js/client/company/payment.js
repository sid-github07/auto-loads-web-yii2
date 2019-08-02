/* global actionCompanyPayments */

/**
 * Changes company payment visible year
 */
function changePaymentYear() {
    var year = $('.payment-year').val();
    $.pjax({
        type: 'POST',
        url: actionCompanyPayments,
        data: {
            year: year
        },
        container: '#company-payment-pjax',
        push: false,
        scrollTo: false,
        cache: false
    });
}