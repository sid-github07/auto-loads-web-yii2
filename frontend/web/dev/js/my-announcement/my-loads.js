/* global actionMyLoadsFiltration, actionLoadAnnouncementForm */

/**
 * Filters my loads table
 *
 * @param {object} element This object
 */
function filterMyLoads(element) {
    var loadCities = getFilteredLoadCities(element);

    updateUrlParam('load-page', 1);
    updateUrlParam('loadCities', loadCities);

    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionMyLoadsFiltration),
        data: {loadCities: loadCities},
        container: '#my-loads-table-pjax',
        push: false,
        replace: false,
        scrollTo: false
    });
}

/**
 * Returns user filtered load cities
 *
 * @param {object} element This element
 * @returns {Array}
 */
function getFilteredLoadCities(element) {
    var loadCities = [];
    var cities = $(element).select2('data');
    $.each(cities, function (key, city) {
        loadCities.push(city.id);
    });
    return loadCities;
}

/**
 * Renders load announcement form
 */
function renderLoadAnnouncementForm() {
    $('#load-editing-form').html("");
    $.pjax({
        type: 'POST',
        url: appendUrlParams(actionLoadAnnouncementForm),
        container: '#announce-load-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#announce-load-modal').modal('show');
        toggleRequiredClass();
        $('.load_cars_editing_container').on('afterInsert', function () {
            toggleRemoveButtonsVisibility();
            toggleRequiredClass();
            validateQuantity();
            toggleNewLoadElementsStructure();
        }).on('afterDelete', function () {
            toggleRemoveButtonsVisibility();
            validateQuantity();
            toggleNewLoadElementsStructure();
        });
    });
}