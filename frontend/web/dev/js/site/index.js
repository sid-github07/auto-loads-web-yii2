/**
 * Executes anonymous function when document fully loads
 */

var  gmapsScriptIncluded = false;
$(document).ready(function () {
    countAnimation();
    addTooltipClass();
});

/**
 * Animates numbers from 0 to value
 */
function countAnimation() {
    $('.counter').each(function () {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 2500,
            easing: 'swing',
            step: function step(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
}

/**
 * Adds custom class to link tooltip
 */
function addTooltipClass() {
    $('.load-loads-link').tooltip({
        template: '<div class="tooltip" role="tooltip">' +
            '<div id="loads-tooltip-arrow" class="tooltip-arrow"></div>' + 
            '<div id="loads-tooltip-inner" class="tooltip-inner">' +
            '</div></div>'
    });
}