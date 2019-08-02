$('#load-open-contacts-modal').on('shown.bs.modal', function () {
    creditServiceInputs();
});

$('#transporter-open-contacts-modal').on('shown.bs.modal', function () {
    creditServiceInputs();
});

function creditServiceInputs() {
    var totalCost = 0, contactDays = 0;
    var costEl = $('#open-contacts-modal-cost');
    var currentCredits = costEl.data('service-credits');
    var contactCredits = costEl.data('open-contacts-cost');
    var subscriptionCredits = costEl.data('subscription-credits');
    var alert = $('#alert');
    var topUp = $('#total-creds-topup');
    var submitBtn = $('#submit-open-contacts');
    var totalCreditEl = $('.total-credits');
   
    $('#open_contacts_days').on('change', function(e) {
        contactDays = $(this).val();
        updateElements();
    });
    
    function updateElements()
    {
        totalCost = contactDays * contactCredits;
        if (totalCost > 0 && totalCost > currentCredits) {
            topUp.show();
            alert.show();
        } else {
            topUp.hide();
            alert.hide();
        }
        
        if (totalCost > currentCredits + subscriptionCredits) {
            submitBtn.attr('disabled', 'disabled').css('opacity', 0.5);
        } else {
            submitBtn.removeAttr('disabled').css('opacity', 1);
        }
        
        totalCreditEl.html(totalCost);
    }
}