/**
 * Adds popover event when select2 shows results
 */
$(document).on('mouseover', '.select2-results__options', function () {
    $('[data-toggle="popover"]').popover({ trigger: 'hover', html: true });
});

/**
 * Shows city location map when user hovers on globe icon
 */
$(document).on('mouseover', '.fa.fa-2x.fa-globe', function () {
    var latitude = Number($(this).data('lat'));
    var longitude = Number($(this).data('lon'));
    var zoom = Number($(this).data('zoom'));
    var mapContainers = document.getElementsByClassName('location-map');
    $.each(mapContainers, function (index, element) {
        showCityLocationMap(latitude, longitude, zoom, element);
    });
});

/**
 * Shows city location map
 *
 * @param {number} lat City location latitude
 * @param {number} lon City location longitude
 * @param {number} zoom Map zoom, which depends on city or country location
 * @param {object} element Map container element
 */
function showCityLocationMap(lat, lon, zoom, element) {
    var coordinates = {lat: lat, lng: lon};
    console.log(element);
    var map = new google.maps.Map(element, {
        zoom: zoom,
        center: coordinates,
        disableDefaultUI: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var marker = new google.maps.Marker({
        position: coordinates,
        map: map
    });
}