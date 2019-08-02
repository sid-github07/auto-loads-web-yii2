/**
 * Returns simple suggestion option
 *
 * @param {object} data Information about the location
 * @returns {string}
 */
function getSimpleSuggestion(data) {
    return "<div>" + data.name + "<div class='pull-right'>" + "<i class='fa fa-2x fa-globe' " + "data-lat='" + data.location.lat + "' " + "data-lon='" + data.location.lon + "' " + "data-zoom='" + data.zoom + "' " + "data-toggle='popover' " + "data-placement='top' " + "data-content='<div class=\"location-map\"></div>'></i>" + "</div>" + "</div>";
}