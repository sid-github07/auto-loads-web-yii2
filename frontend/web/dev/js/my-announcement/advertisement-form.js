$(document).ready(function() {
    let carSelect = $('document.adv-credits-cost option:selected');
    let daySelect = $('document.adv-total-credits option:selected');
    let valueSpan = $('document#cred-val');
    let valueInput = $('document#cred-val-form');
    let totalCreditsInput = $('document#adv-credits');
    let credits = 10;

    bindEvents();
    displayTopUpSpanIfPossible();

    function displayTopUpSpanIfPossible()
    {
        if (totalCreditsInput.val() > valueInput.val()) {
            $('#total-creds-topup').hide();
        } else {
            $('#total-creds-topup').hide();
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
        return parseInt(carSelect.text()) * parseInt(daySelect.text());
    }

    function bindEvents()
    {
        $('document').on('change', '.adv-credits-cost',function(e) {
            credits = calculateCredits(carSelect, daySelect);
            valueSpan.text(credits);
            valueInput.val(credits);
            displayTopUpSpanIfPossible();
        });

        $('document').on('change', '.adv-credits-cost', function(e) {
            credits = calculateCredits(carSelect, daySelect);
            valueSpan.text(credits);
            valueInput.val(credits);
            displayTopUpSpanIfPossible();
        });
    }
});