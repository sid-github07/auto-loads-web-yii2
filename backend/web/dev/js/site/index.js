/* global timezoneOffset, actionSetTimezoneOffset */

/**
 * Executes anonymous function when document fully loads
 */
$(document).ready(function () {
    if (timezoneOffset === '') {
        /** @member {number} Administrator timezone offset */
        var offset = calculateTimezoneOffset();
        setTimezoneOffset(offset);
    }
});

/**
 * Returns administrator timezone offset
 *
 * @returns {number}
 */
function calculateTimezoneOffset () {
    /** @member {object} Date object */
    var date = new Date();
    return -date.getTimezoneOffset() / 60;
}

/**
 * Sets user timezone offset
 *
 * @param {number} offset Administrator timezone offset
 */
function setTimezoneOffset (offset) {
    $.post(actionSetTimezoneOffset, {
        offset: offset
    });
}