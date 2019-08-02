
$('#adv-load-modal').on('shown.bs.modal', function () {
    advFormInputs();
});

function advFormInputs() {
    let carSelect, daySelect;
    let creditsCostSpan = $('.cred-val');
    let creditsCostInput = $('.credits');
    let serviceCreditsInput = $('.service-credits');
    let subscriptionCreditsInput = $('.subscription-credits');
    let totalCredits = parseInt(serviceCreditsInput.val()) + parseInt(subscriptionCreditsInput.val());
    let credits = creditsCostInput.val();

    bindEvents();
    displayDomElementsIfPossible(creditsCostInput.val(), serviceCreditsInput.val());

    /**
     *
     * @param purchaseSum
     */
    function displayDomElementsIfPossible(purchaseSum)
    {
        if (serviceCreditsInput.val() == 0) {
            $('body #total-creds-topup').show();
            $('#alert').show();
        } else {
            $('body #total-creds-topup').hide();
            $('#alert').hide();
        }

        if (purchaseSum > totalCredits) {
            $('body #submit-adv').attr('disabled', 'true').css('opacity', 0.5);
        } else {
            $('body #submit-adv').removeAttr('disabled').css('opacity', 1);
        }
    }

    /**
     *
     * @param carSelect
     * @param daySelect
     * @returns {number}
     */
    function calculateCredits(carSelect, daySelect)
    {
        let cars = carSelect.val();
        let days = daySelect.val();

        return days * cars
    }

    /** binds select events **/
    function bindEvents()
    {
        $('body').on('change', '#load-days_adv',function(e) {
            
            creditsCostSpan = $(e.target).closest('.modal-body').find('.cred-val');
            creditsCostInput = $(e.target).closest('.modal-body').find('.credits');

            carSelect = $(e.target).closest('.modal-body').find('#load-car_pos_adv');
            credits = calculateCredits(carSelect, $(this));

            creditsCostSpan.text(credits);
            creditsCostInput.val(credits);

            displayDomElementsIfPossible(credits);
        });

        $('body').on('change', '#load-car_pos_adv', function(e) {

            creditsCostSpan = $(e.target).closest('.modal-body').find('.cred-val');
            creditsCostInput = $(e.target).closest('.modal-body').find('.credits');

            daySelect = $(e.target).closest('.modal-body').find('#load-days_adv');
            credits = calculateCredits($(this), daySelect);

            creditsCostSpan.text(credits);
            creditsCostInput.val(credits);

            displayDomElementsIfPossible(credits);
        });
    }
}