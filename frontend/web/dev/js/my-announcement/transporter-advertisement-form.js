$(document).ready(function() {
    let carSelect, daySelect;
    let creditsCostSpan = $('body .cred-val');
    let creditsCostInput = $('body .credits');
    let serviceCreditsInput = $('.service-credits');
    let subscriptionCreditsInput = $('.subscription-credits');
    let totalCredits = serviceCreditsInput.val() + subscriptionCreditsInput.val();
    let credits = creditsCostInput.val();

    bindEvents();

    displayDomElementsIfPossible(creditsCostInput.val());

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
            $('#submit-adv-tr').attr('disabled', 'true').css('opacity', 0.5);
        } else {
            $('#submit-adv-tr').removeAttr('disabled').css('opacity', 1);
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
        $('body').on('change', '#cartransporter-days_adv',function(e) {

            creditsCostSpan = $(e.target).closest('.modal-body').find('.cred-val');
            creditsCostInput = $(e.target).closest('.modal-body').find('.credits');

            carSelect = $(e.target).closest('.modal-body').find('#cartransporter-car_pos_adv');
            credits = calculateCredits(carSelect, $(this));

            creditsCostSpan.text(credits);
            creditsCostInput.val(credits);

            displayDomElementsIfPossible(credits);
        });

        $('body').on('change', '#cartransporter-car_pos_adv', function(e) {

            creditsCostSpan = $(e.target).closest('.modal-body').find('.cred-val');
            creditsCostInput = $(e.target).closest('.modal-body').find('.credits');

            daySelect = $(e.target).closest('.modal-body').find('#cartransporter-days_adv');
            credits = calculateCredits($(this), daySelect);

            creditsCostSpan.text(credits);
            creditsCostInput.val(credits);

            displayDomElementsIfPossible(credits);
        });
    }
});