/* global TEXT_HIDE_MAP, TEXT_SHOW_MAP, actionPreview, actionPreviewLink */

var isFullScreen = false;
var mapRendered = false;
var filtersRendered = false;
var mapOpen = false, filtersOpen = false;

$(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
    isFullScreen = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen;
});

$(document).ready(function($){
    $('body').on('click', '.map-link', function(e) {
        logOpenMapAction();
    });
    $('body').on('click', '.btn-text', function(e) {
        var btn = $(this).parent().find('input[type=\'button\']');
        btn.trigger('click');
    });
    $('body').on('click', '.btn-icon', function(e) {
        var btn = $(this).parent().find('input[type=\'button\']');
        btn.trigger('click');
    });

    $('body').on('click', '.map-button', function(e){

        var button = $(this);
        var btnText = button.find('.btn-text');
        var btnIcon = button.find('.btn-icon');

        var container =  button.parent().find('.map-container');
        var contactMapRendered = $(this).data('rendered');
        var mapOpen = $(this).data('map-open');

        if (contactMapRendered === false) {
            renderLoadContactMapAjax(this, container);
            btnText.html(closeMapText);
            btnIcon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
        }

        if (mapOpen === true && contactMapRendered === true) {
            btnText.html(openMapText);
            container.fadeOut();
            button.data('map-open', false);
            btnIcon.removeClass('fa-minus-circle').addClass('fa-plus-circle');

        } else if (mapOpen === false && contactMapRendered === true) {
            btnText.html(closeMapText);
            container.fadeIn();
            button.data('map-open', true);
            btnIcon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
        }

    });

    if (needToOpenFilters === true) {
        $('#filter-btn').click();
    }
});

/**
 * Sends request to backend to register map open action for statistics
 */
function logOpenMapAction() {
    $.post(actionLogTransporterMapOpen, {});
}

/**
 * Changes map collapse button text depending on whether map is expanded or not
 *
 * @param {object} element This element
 */
function changeMapCollapseButtonText(element,event) {
    event.preventDefault();
    if (mapOpen) {
        var html = '<i class="fa fa-plus-circle btn-icon map-link"></i> <span class="btn-text map-link">' + TEXT_SHOW_MAP + '</span>';
    } else {
        var html = '<i class="fa fa-minus-circle margin-right-5 btn-icon"></i><span class="btn-text color-black">' + TEXT_HIDE_MAP + '</span>';
    }

    var siblingElems = $(element).siblings();
    siblingElems.each((index, item) => {
        item.remove();
    });
    $(element).parent().append(html);
    if (mapRendered === false) {
        renderMapAjax();
    }
    mapOpen = !mapOpen;
}

function changeFiltersCollapseButtonText(element,event) {
    event.preventDefault();
    if (filtersOpen) {
        var html = '<i class="fa fa-plus-circle btn-icon margin-right-5"></i><span class="btn-text">' + TEXT_SHOW_FILTERS + '</span>';
    } else {
        var html = '<i class="fa fa-minus-circle margin-right-5 btn-icon"></i><span class="btn-text color-black">' + TEXT_HIDE_FILTERS + '</span>';
    }
    var siblingElems = $(element).siblings();
    siblingElems.each((index, item) => {
        item.remove();
    });


    $(element).parent().append(html);
    if (filtersRendered === false) {
        renderFiltersAjax();
    }
    filtersOpen = !filtersOpen;
}

/**
 * Filters car transporters map by unload city or country
 *
 * @param {object} element This object
 */
function filterByUnloadCity(element) {
    var unloadCityId = $(element).val();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('unloadCityId', unloadCityId, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
    location.reload();
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

    if (value === '') {
        return query;
    }

    return (query.length > 2 ? query + "&" : "?") + (value ? param + "=" + value : '');
}

/**
 * Changes entries per page size
 *
 * @param {object} element This object
 */
function changePageSize(element) {
    var pageSize = $(element).val();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('pageSize', pageSize, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
    location.reload();
}

/**
 * Renders car transporter owner contact info preview
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function previewContactInfo(e, id) {
    e.preventDefault();
    if (isFullScreen) {
        $('#map_canvas div.gm-style button[title="Toggle fullscreen view"]').trigger('click');
    }
    $.pjax({
        type: 'POST',
        url: actionPreview,
        data: {
            id: id,
            showInfo: true
        },
        container: '#contact-info-preview-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showContactInfoPreviewModal();
    });
}

/**
 * Shows contact info preview modal
 */
function showContactInfoPreviewModal() {
    $('#contact-info-preview-modal').modal('show');
}

/**
 * Shows or hides car transporter preview
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function collapseCarTransporterPreview(e, id) {
    e.preventDefault();

    var td = $('#car-transporter-preview-' + id);
    var div = td.find('.content');
    var tr = td.parent();

    if (div.text().length === 0) {
        $.post(actionPreview, {id: id, showInfo: false}, function (content) {
            div.html(content);
            tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
        });
    } else {
        tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
    }
}

/**
 * Checks credit code and shows car transporter
 * preview and Toast Messages dependend on credit code.
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function refreshCarTransporterPreview(e, id) {
    e.preventDefault();
    var td = $('#car-transporter-preview-' + id);
    $.ajax({
        type: 'POST',
        url: $('#car-transporter-creditcode-form').attr('action'),
        data: $('#car-transporter-creditcode-form').serialize(),
        dataType: 'html',
        success: function(data) {
            $.ajax({
                type: 'GET',
                url: 'car-transporter/get-msgs-creditcode-state',
                dataType: 'json',
                success: function(msgdata) {
                    if (msgdata.type != '') {
                        var cfg = {'closeButton':true,'debug':false,'newestOnTop':true,'progressBar':false,'positionClass':'toast-top-center','preventDuplicates':true,'showDuration':0,'hideDuration':1000,'timeOut':45000,'extendedTimeOut':8000,'onShown':function() { $('.alert-container').append($('#toast-container'));}};
                        switch(msgdata.type) {
                            case 'error':
                                toastr.error(msgdata.message, '', cfg);
                                break;
                            case 'success':
                                toastr.success(msgdata.message, '', cfg);
                                break;
                        }
                    }
                    td.html(data);
                }
            });
        }
    });
}

/**
 * Shows car transporter link
 *
 * @param {object} e Event object
 * @param {number} id Car transporter ID
 */
function showCarTransporterLink(e, id) {
    e.preventDefault();

    $.pjax({
        type: 'POST',
        url: actionPreviewLink,
        data: {id: id},
        container: '#car-transporter-link-preview-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#car-transporter-link-preview-modal').modal('show');
    });
}

/**
 * Copies car transporter link to clipboard
 */
function copyCarTransporterLinkToClipboard() {
    $('#car-transporter-link-field').select();
    if (document.execCommand('Copy')) {
        $("#car-transporter-link-success-alert").fadeIn('slow', function() {
            $(this).removeClass('hidden');
        });
        setTimeout(function() {
            $("#car-transporter-link-success-alert").fadeOut('slow', function() {
                $(this).addClass('hidden');
            });
        }, 5000); // 5 seconds
    }
}

function renderMapAjax()
{
    $.pjax({
        type: 'POST',
        url: actionRenderMap + window.location.search,
        container: '#C-T-3',
        cache: false,
        async: true,
        push: false,
    }).done(function () {
        mapRendered = true;
        loadScript();
    });
}

/**
 *
 * @param data
 */
function renderFiltersAjax(data)
{
    $.pjax({
        type: 'POST',
        url: actionRenderFilters + window.location.search,
        container: '#filter',
        cache: false,
        async: true,
        push: false,
    }).done(function () {
        filtersRendered = true;
    });
}


function renderLoadContactMapAjax(e, container)
{
    var transporterID = $(e).data('transporter');
    $.ajax({
        method: 'POST',
        url: actionRenderContactMap,
        data: {transporter: transporterID},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        success: function(data) {
            container.html(data);
            $(e).data('rendered', true);
            $(e).data('map-open', true)
        }
    });
}