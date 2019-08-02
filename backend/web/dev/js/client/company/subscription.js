/* global actionCompanySubscriptions, actionRenderNewSubscriptionForm, actionGetSubscriptionRange, actionChangeSubscriptionEndDate, actionChangeSubscriptionActivity */

/**
 * Changes company subscription end date value
 *
 * @param {number} id Active subscription ID
 */
function changeSubscriptionEndDate(id) {
    var date = $('.end-date-' + id).val();
    $.post(actionChangeSubscriptionEndDate, {id: id, date: date}, function () {
        $.pjax.reload({container: '#company-index-pjax'});
    });
}

/**
 * Changes company subscription visible year
 */
function changeYear() {
    var year = $('.subscription-year').val();
    $.pjax({
        type: 'POST',
        url: actionCompanySubscriptions,
        data: {
            year: year
        },
        container: '#company-subscriptions-pjax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Changes company subscription activity
 *
 * @param {number} id Active subscription ID
 */
function changeSubscriptionActivity(id, userId) {
    var status = $('.status-' + id).is(':checked');
    $.post(actionChangeSubscriptionActivity, {id: id, status: status, userId: userId}, function () {
        $.pjax.reload({container: '#company-index-pjax'});
    });
}

/**
 * Renders new subscription creation form in modal
 */
function createNewSubscription() {
    $.pjax({
        type: 'POST',
        url: actionRenderNewSubscriptionForm,
        container: '#new-subscription-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showNewSubscriptionModal();
    });
}

/**
 * Shows new company subscription creation modal
 */
function showNewSubscriptionModal() {
    $('#new-subscription-modal').modal('show');
}

/**
 * Fills new company subscription date range fields with date depending on selected service
 */
function fillSubscriptionDate() {
    var serviceId = $('.service-selection').val();
    $.post(actionGetSubscriptionRange, {serviceId: serviceId}, function (data) {
        if (data == '') {
            return null;
        } else {
            var range = $.parseJSON(data);
            var startDate = range[0];
            var endDate = range[1];
            $('.date_of_purchase').val(startDate);
            $('.end_date').val(endDate);
        }
    });
}