function creditServiceInputs() {
    var carCount = 0, carDays = 0, totalCost = 0, contactDays = 0, contactCredits = 0;
    var currentCredits = $('.total-credits').data('credits');
    var contactCredits = $('#open-contacts-cost').data('open-contacts-cost');
    var subscriptionCredits = $('.total-credits').data('subscription-credits');
    var alert = $('.adv-alert');
    var submitBtn = $('#IA-C-15');
    var topUp = $('#total-creds-topup-tr');

    $('#car-count').on('change', function(evt) {
        carCount = parseInt($(this).val());
        updateElements();
    });

    $('#car-days').on('change', function(evt) {
        carDays = parseInt($(this).val());
        updateElements();
    });
    
    $('#open_contacts_days').on('change', function(evt) {
        contactDays = $(this).val();
        updateElements();
    });
    
    /**
     * Check whether advertisement select inputs are set to zero
     * 
     * @returns boolean
     */
    function advertisementInputsNotNull() {
        return $('#car-count').val() != 0 || $('#car-days').val() != 0;
    }
    
    function updateElements()
    {
        totalCost = (carCount * carDays) + (contactDays * contactCredits);
        
        if ((totalCost > 0 && totalCost > currentCredits) || (currentCredits == 0 && advertisementInputsNotNull())) {
            topUp.show();
            alert.show();
        } else {
            topUp.hide();
            alert.hide();
        }
        
        if (totalCost > currentCredits + subscriptionCredits) {
            submitBtn.attr('disabled', 'disabled');
            submitBtn.addClass('disabled-btn');
        } else {
            submitBtn.attr('disabled', false);
            submitBtn.removeClass('disabled-btn');
        }
        
        $('.cred-val').html(totalCost);
    }
}

$(document).ready(function () {
    creditServiceInputs();
});

$('#announce-load-modal').on('shown.bs.modal', function () {
    creditServiceInputs();
});

