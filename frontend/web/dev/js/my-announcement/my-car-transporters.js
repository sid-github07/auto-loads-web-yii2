/* global actionCarTransporterAnnouncementForm, actionMyCarTransportersFiltration */

/**
 * Renders car transporter announcement form
 */
function renderCarTransporterAnnouncementForm() {
    $.pjax({
        type: 'POST',
        url: actionCarTransporterAnnouncementForm,
        container: '#announce-car-transporter-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#announce-car-transporter-modal').modal('show');
    });
}

/**
 * Filters my car transporters table
 *
 * @param {object} element This object
 */
function filterMyCarTransporters(element) {
    var carTransporterCities = getFilteredCarTransporterCities(element);

    updateUrlParam('car-transporter-page', 1);
    updateUrlParam('carTransporterCities', carTransporterCities);

    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionMyCarTransportersFiltration),
        data: {carTransporterCities: carTransporterCities},
        container: '#my-car-transporters-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    });
}

/**
 * Returns user filtered car transporter cities
 *
 * @param {object} element This element
 * @returns {Array}
 */
function getFilteredCarTransporterCities(element) {
    var carTransporterCities = [];
    var cities = $(element).select2('data');

    $.each(cities, function (key, city) {
        carTransporterCities.push(city.id);
    });

    return carTransporterCities;
}