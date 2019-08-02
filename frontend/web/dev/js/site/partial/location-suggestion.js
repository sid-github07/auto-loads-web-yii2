/**
 * Returns simple suggestion option
 *
 * @param {object} data Information about the location
 * @returns {string}
 */
function getSimpleSuggestion(data) {
    return "<div>" + data.name + "<div class='pull-right'>" + "<span class='map-text' " + "data-lat='" + data.location.lat + "' " + "data-lon='" + data.location.lon + "' " + "data-zoom='" + data.zoom + "' " + "data-toggle='popover' " + "data-placement='top' " + "data-content='<div class=\"load-city-map\"></div>'>" + mapTranslate + "</span>" + "</div>" + "</div>";
}