/* global serviceSelectionUrl, serviceConfirmationUrl, servicePurchaseUrl, backToServicePurchaseUrl, serviceActivationUrl, pjaxContainerId */

/**
 * Renders service confirmation page
 *
 * @param {number} serviceId Selected service ID
 */
function renderServiceConfirmation(serviceId) {
    $.pjax({
        type: 'POST',
        url: serviceConfirmationUrl,
        data: {
            serviceId: serviceId
        },
        container: '#' + pjaxContainerId,
        push: false,
        scrollTo: false
    });
}

/**
 * Registers service selection event and goes back to confirmation page
 */
function loadConfirmationStep() {
    $('.service, .back-to-confirmation').unbind().on('click', function () {
        /** @memberOf {number} Selected service ID */
        var serviceId = $(this).data('service-id');
        reloadContainerId(this);
        renderServiceConfirmation(serviceId);
    });
}

/**
 * Cancels selected service
 */
function cancelPurchase() {
    $('.cancel-purchase').click(function () {
        var serviceTypeId = $(this).data('service-type-id');
        reloadContainerId(this);
        renderServiceSelection(serviceTypeId);
    });
}

/**
 * Renders service selection page
 * @param serviceTypeId
 */
function renderServiceSelection(serviceTypeId) {
    $.pjax({
        type: 'POST',
        url: serviceSelectionUrl,
        data: {
            serviceTypeId: serviceTypeId
        },
        container: '#' + pjaxContainerId,
        push: false,
        scrollTo: false
    });
}

/**
 * Removes currently selected service from cart
 */
function removeServiceFromCart() {
    $('.remove-from-cart .link').click(function (e) {
        e.preventDefault();
        renderServiceSelection();
    });
}

/**
 * Renders service purchase page
 *
 * @param {number} serviceId Selected service ID
 */
function renderServicePurchase(serviceId) {
    $.pjax({
        type: 'POST',
        url: servicePurchaseUrl,
        data: {
            serviceId: serviceId
        },
        container: '#' + pjaxContainerId,
        push: false,
        scrollTo: false
    });
}

/**
 * Registers service purchase event
 */
function purchaseService() {
    $('.purchase').unbind().click(function () {
        var serviceId = $(this).data('service-id');
        reloadContainerId(this);
        renderServicePurchase(serviceId);
    });
}

/**
 * Renders back to service purchase page
 *
 * @param {number} userServiceId User service ID
 */
function renderBackToServicePurchase(userServiceId) {
    $.pjax({
        type: 'POST',
        url: backToServicePurchaseUrl,
        data: {
            userServiceId: userServiceId
        },
        container: '#' + pjaxContainerId,
        push: false,
        scrollTo: false
    });
}

/**
 * Registers back to service purchase event
 */
function backToServicePurchase() {
    $('.back-to-purchase').click(function () {
        var userServiceId = $(this).data('user-service-id');
        reloadContainerId(this);
        renderBackToServicePurchase(userServiceId);
    });
}

/**
 * Renders service activation page
 *
 * @param {number} userServiceId
 */
function renderServiceActivation(userServiceId) {
    $.pjax({
        type: 'POST',
        url: serviceActivationUrl,
        data: {
            userServiceId: userServiceId
        },
        container: '#' + pjaxContainerId,
        push: false,
        scrollTo: false
    });
}

/**
 * Registers pay later for service event
 */
function payLater() {
    $('.pay-later').click(function () {
        var userServiceId = $(this).data('user-service-id');
        renderServiceActivation(userServiceId);
    });
}

/**
 * Executes anonymous function when documents fully loads
 */
$(document).ready(function () {
    /** Renders service selection event */
    loadConfirmationStep();
});

function reloadContainerId(button)
{
    var closesContainerId = $(button).closest('[data-pjax-container]').attr('id');
    if (closesContainerId !== undefined) {
        pjaxContainerId = closesContainerId
    }
}
/**
 * Executes anonymous function when documents fully loads
 */
$(document).on('pjax:end', function () {
    /** Renders service selection event */
    loadConfirmationStep();

    /** Cancels selected service */
    cancelPurchase();

    /** Removes currently selected service from cart */
    removeServiceFromCart();

    /** Registers service purchase event */
    purchaseService();

    /** Registers back to service purchase event */
    backToServicePurchase();

    /** Registers pay later for service event */
    payLater();
});
