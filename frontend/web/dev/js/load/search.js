'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

/**
 * Executes anonymous function when document is ready
 */
$(document).ready(function () {
    registerLoadDirectionChange();
});

/**
 * Registers load direction change event
 */
function registerLoadDirectionChange() {
    $('#IK-C-10').unbind('change.direction').bind('change.direction', function () {
        loadDirectionChange();
    });
}

/**
 * Updates load and unload cities when user selects city with direction
 */
function loadDirectionChange() {
    /** @member {object|string} Object of cities IDs or City ID string */
    var cities = $('#IK-C-10').val();
    var load;
    var unload;
    if (isMultipleCities(cities)) {
        $.each(cities, function (key, city) {
            if (!isDirection(city)) {
                return;
            }

            var _splitDirection = splitDirection(city);

            var _splitDirection2 = _slicedToArray(_splitDirection, 2);

            load = _splitDirection2[0];
            unload = _splitDirection2[1];

            updateCities(city, load, 'load');
            updateCities(city, unload, 'unload');
        });
    } else {
        if (!isDirection(cities)) {
            return;
        }

        var _splitDirection3 = splitDirection(cities);

        var _splitDirection4 = _slicedToArray(_splitDirection3, 2);

        load = _splitDirection4[0];
        unload = _splitDirection4[1];

        updateCity(load, 'load');
        updateCity(unload, 'unload');
    }
}

/**
 * Checks whether searching multiple cities
 *
 * @param {object|string} cities Object of cities IDs or City ID string
 * @returns {boolean}
 */
function isMultipleCities(cities) {
    return (typeof cities === 'undefined' ? 'undefined' : _typeof(cities)) == 'object';
}

/**
 * Updates load/unload cities
 *
 * @param {string} direction Direction ID
 * @param {string} city City ID
 * @param {string} type Word: 'load' or 'unload'
 */
function updateCities(direction, city, type) {
    /** @member {string} Load/unload input ID */
    var selector = type === 'load' ? '#IK-C-10' : '#IK-C-11';
    /** @member {null|object} Load/unload cities */
    var value = $(selector).val();
    /** @member {object} Load/unload cities */
    var cities = value === null ? [] : value;

    removeDirectionOption(selector, direction);
    appendCities(selector, city);
    supplementCities(selector, city, cities);
}

/**
 * Removes direction option from load/unload input
 *
 * @param {string} selector Load/unload input ID
 * @param {string} direction Direction ID
 */
function removeDirectionOption(selector, direction) {
    $(selector).find('option[value="' + direction + '"]').remove();
}

/**
 * Appends city
 *
 * @param {string} selector Load/unload input ID
 * @param {string} city City ID
 */
function appendCities(selector, city) {
    if ($(selector).find('option[value="' + city + '"]').length == 0) {
        $(selector).append($('<option></option>').attr('value', city));
    }
}

/**
 * Supplements cities
 *
 * @param {string} selector Load/unload input ID
 * @param {string} city City ID
 * @param {object} cities List of load/unload cities
 */
function supplementCities(selector, city, cities) {
    cities.push(city);
    $(selector).val(cities).change();
}

/**
 * Updates city
 *
 * @param {string} city City ID
 * @param {string} type Word: 'load' or 'unload'
 */
function updateCity(city, type) {
    /** @member {string} Load or unload city input ID */
    var selector = type === 'load' ? '#IK-C-10' : '#IK-C-11';
    $(selector).append($('<option></option>').attr('value', city)).val(city).change();
}

/**
 * Checks whether selected city is city with direction
 *
 * @param {string} city Selected city ID
 * @returns {boolean}
 */
function isDirection(city) {
    return city.indexOf('-') >= 0;
}

/**
 * Splits direction to load and unload cities
 *
 * @param {string} direction
 * @returns {[{string},{string}]}
 */
function splitDirection(direction) {
    /** @member {string} Load city ID */
    var load = direction.split('-')[0];
    /** @member {string} Unload city ID */
    var unload = direction.split('-')[1];

    return [load, unload];
}