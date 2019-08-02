/**
 * Changes URL address on tab selection
 *
 * @param {object} e Event
 * @param {string} tab Selected tab
 * @param {boolean} saveToCookie Attribute, whether save current tab to cookie
 */
function changeTabUrl(e, tab, saveToCookie) {
    e.preventDefault();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('tab', tab, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);

    if (saveToCookie !== undefined && saveToCookie === true) {
        saveTabToCookie(tab);
    }
}

/**
 * Saves user selected tab to cookie
 *
 * @param {string} tab User selected tab name
 */
function saveTabToCookie(tab) {
    document.cookie = "tab=" + tab + "; path=/";
}

/**
 * Replaces query params
 *
 * @see {@link http://stackoverflow.com/a/19472410/5747867}
 * @param {string} param
 * @param {string} value
 * @param {string} search
 * @returns {string}
 */
function replaceQueryParam(param, value, search) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');

    return (query.length > 2 ? query + "&" : "?") + (value ? param + "=" + value : '');
}

/**
 * Returns specific param from URL or returns list of all URL params
 *
 * @see https://www.creativejuiz.fr/blog/en/javascript-en/read-url-get-parameters-with-javascript
 * @param param
 * @returns {*}
 */
function $_GET(param) {
    var vars = {};
    window.location.href.replace(location.hash, '').replace(/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
    function (m, key, value) {
        // callback
            vars[key] = value !== undefined ? value : '';
    });

    if ( param ) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}

/**
 * Appends given action URL with params
 *
 * @param {string} url URL address to specific action
 * @returns {string}
 */
function appendUrlParams(url) {
    var params = ['load-page', 'load-per-page', 'loadCities', 'car-transporter-page', 'car-transporter-per-page', 'load-activity', 'car-transporter-activity', 'carTransporterCities'];
    $.each(params, function (index, param) {
        var value = $_GET(param);
        url = replaceQueryParam(param, value, url);
    });

    return url;
}

/**
 * Updates specific URL parameter with given value
 *
 * @param {string} param URL param name
 * @param {number|Array|string} value Value to be set to specific URL param
 */
function updateUrlParam(param, value) {
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam(param, value, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
}