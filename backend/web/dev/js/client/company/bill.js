/* global
actionCompanyInvoices,
actionCompanyPreInvoices,
actionRenderPreInvoiceCreationForm,
subscriptions,
ACTION_ADD_SUBSCRIPTION_TO_LIST,
ACTION_REMOVE_SUBSCRIPTION_FROM_LIST */

/**
 * Changes company invoice visible year
 */
function changeInvoiceYear() {
    var year = $('.invoice-year').val();
    $.pjax({
        type: 'POST',
        url: actionCompanyInvoices,
        data: {
            year: year
        },
        container: '#company-bill-ajax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Changes company pre-invoice visible year
 */
function changePreInvoiceYear() {
    var year = $('.pre-invoice-year').val();
    $.pjax({
        type: 'POST',
        url: actionCompanyPreInvoices,
        data: {
            year: year
        },
        container: '#company-bill-ajax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Renders pre-invoice creation form in modal
 */
function createPreInvoice() {
    $.pjax({
        type: 'POST',
        url: actionRenderPreInvoiceCreationForm,
        container: '#pre-invoice-creation-pjax',
        push: false,
        scrollTo: false,
        cache: false
    }).done(function () {
        showCreatePreInvoiceModal();
    });
}

/**
 * Shows pre-invoice creation modal
 */
function showCreatePreInvoiceModal() {
    $('#pre-invoice-creation-modal').modal('show');
}

/**
 * Updates subscriptions list
 */
function updateSubscriptions() {
    $.pjax({
        type: 'POST',
        url: actionRenderPreInvoiceCreationForm,
        data: {
            serviceTypeId: getServiceTypeId(),
            user: getUser(),
            startDate: getStartDate()
        },
        container: '#pre-invoice-creation-pjax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Adds subscription to subscriptions list
 */
function addSubscriptionToList() {
    $.pjax({
        type: 'POST',
        url: actionRenderPreInvoiceCreationForm,
        data: {
            serviceTypeId: getServiceTypeId(),
            subscription: getSubscription(),
            subscriptions: subscriptions,
            user: getUser(),
            startDate: getStartDate(),
            action: ACTION_ADD_SUBSCRIPTION_TO_LIST
        },
        container: '#pre-invoice-creation-pjax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Removes subscription from subscriptions list
 *
 * @param {object} e Event object
 * @param {number} id Subscription ID that needs to be removed from subscriptions list
 */
function removeSubscriptionFromList(e, id) {
    e.preventDefault();
    $.pjax({
        type: 'POST',
        url: actionRenderPreInvoiceCreationForm,
        data: {
            serviceTypeId: getServiceTypeId(),
            subscription: getSubscription(),
            subscriptions: subscriptions,
            serviceToRemove: id,
            user: getUser(),
            startDate: getStartDate(),
            action: ACTION_REMOVE_SUBSCRIPTION_FROM_LIST
        },
        container: '#pre-invoice-creation-pjax',
        push: false,
        scrollTo: false,
        cache: false
    });
}

/**
 * Returns selected service type ID
 *
 * @returns {*|jQuery}
 */
function getServiceTypeId() {
    return $('#types').val();
}

/**
 * Returns selected subscription value
 *
 * @returns {*|jQuery}
 */
function getSubscription() {
    return $('.subscriptions').val();
}

/**
 * Returns selected pre-invoice user
 *
 * @returns {*|jQuery}
 */
function getUser() {
    return $('.user').val();
}

/**
 * Returns selected pre-invoice start date
 *
 * @returns {*|jQuery}
 */
function getStartDate() {
    return $('.start-date').val();
}