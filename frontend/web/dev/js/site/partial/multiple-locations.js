var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* global loadId, unloadId, oldLocations */

var archivedLocations = {};

/**
 * Archives locations options
 *
 * @param {object} locations List of locations objects
 */
function archiveLocations(locations) {
  $.each(locations, function (key, location) {
    if (!(location.id in archivedLocations)) {
      archivedLocations[location.id] = location;
    }
  });
}

/**
 * Checks whether location is a direction
 *
 * @param {object} data Information about the location
 * @returns {boolean}
 */
function isDirection(data) {
  return _typeof(data.directionId) !== (typeof undefined === "undefined" ? "undefined" : _typeof(undefined)) && data.directionId !== false;
}

/**
 * Returns direction suggestion option
 *
 * @param {string} name Location name
 * @returns {string}
 */
function getDirectionSuggestion(name) {
  return "<div class='direction'>" + name + "</div>";
}

/**
 * Returns simple suggestion option
 *
 * @param {object} data Information about the location
 * @returns {string}
 */
function getSimpleSuggestion(data) {
  return "<div>" + data.name + "<div class='pull-right'>" + "<span class='map-text'" + "data-lat='" + data.location.lat + "' " + "data-lon='" + data.location.lon + "' " + "data-zoom='" + data.zoom + "' " + "data-toggle='popover' " + "data-placement='top' " + "data-content='<div class=\"load-city-map\"></div>'> " + translateMap + "</span>" + "</div>" + "</div>";
}

/**
 * Returns location name
 *
 * @param {object} currentLocation Currently selected location object
 * @returns {string}
 */
function getLocationName(currentLocation) {
  if ($(currentLocation).attr('text') != '') {
    return currentLocation.text;
  }

  var name = '';
  $.each(archivedLocations, function (archivedLocationId, archivedLocation) {
    if (currentLocation.id == archivedLocationId) {
      if (isDirection(archivedLocation)) {
        removeLocationOption(loadId, archivedLocationId);
        updateLocation(loadId, archivedLocation.popularId, archivedLocation.popularName);
        updateLocation(unloadId, archivedLocation.directionId, archivedLocation.directionName);
      } else {
        name = archivedLocation.name;
      }
    }
  });
  return name;
}

/**
 * Removes location option from select element
 *
 * @param {string} elementId Select element ID
 * @param {string} locationId Select element option value
 */
function removeLocationOption(elementId, locationId) {
  $('#' + elementId).find('option[value="' + locationId + '"]').remove();
}

/**
 * Updates location information in select element
 *
 * @param {string} elementId Select element ID
 * @param {string} locationId Select element option value
 * @param {string} locationName Location name
 */
function updateLocation(elementId, locationId, locationName) {
  if (!isLocationExists(elementId, locationId)) {
    var selector = '#' + elementId;
    var selectedLocations = $(selector).val() === null ? [] : $(selector).val();
    var option = $('<option></option>').attr('value', locationId).html(locationName);
    $(selector).append(option);
    selectedLocations.push(locationId);
    $(selector).val(selectedLocations).change();
  }
}

/**
 * Checks whether location is already included in selector
 *
 * @param {string} elementId Select element ID
 * @param {string} locationId Select element option value
 * @returns {boolean}
 */
function isLocationExists(elementId, locationId) {
  return $('#' + elementId).find('option[value="' + locationId + '"]').length > 0;
}

/**
 * Removes unselected locations
 *
 * @param {string} elementId Select element ID
 */
function removeLocation(elementId) {
  var oldLocations = getLocationsBeforeRemove('#' + elementId);
  var currentLocations = $('#' + elementId).val();
  var locationsToRemove = $(oldLocations).not(currentLocations).get();

  $.each(locationsToRemove, function (key, locationId) {
    removeLocationOption(elementId, locationId);
  });
}

/**
 * Returns locations IDs before remove event
 *
 * @param {string} selector Select element ID
 * @returns {Array}
 */
function getLocationsBeforeRemove(selector) {
  var oldLocations = [];
  $(selector + ' option').each(function () {
    oldLocations.push($(this).val());
  });

  return oldLocations;
}
