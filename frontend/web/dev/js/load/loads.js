/* global TEXT_HIDE_MAP, TEXT_SHOW_MAP, actionPreviewLoadInfo, actionPreviewLoadLink, actionPreviewExpiredLoadInfo */

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
    $.post(actionLogLoadMapOpen, {});
}

/**
 * Renders load info preview
 *
 * @param {object} e Event object
 * @param {number} id Load ID
 */
function previewLoadInfo(e, id) {
    e.preventDefault();
    if (isFullScreen) {
        $('#map_canvas div.gm-style button[title="Toggle fullscreen view"]').trigger('click');
    }
    $.pjax({
        type: 'POST',
        url: actionPreviewLoadInfo,
        data: {
            id: id,
            showLoadInfo: true
        },
        container: '#load-info-preview-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        showLoadInfoPreviewModal();
    });
}

/**
 * Shows load info preview modal
 */
function showLoadInfoPreviewModal() {
    $('#load-info-preview-modal').modal('show');
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
 * Filters loads map by load city or country
 *
 * @param {object} element This object
 */
function filterByLoadCity(element) {
    var loadCityId = $(element).val();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('loadCityId', loadCityId, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
    location.reload();
}

/**
 * Filters loads map by unload city or country
 *
 * @param element
 */
function filterByUnloadCity(element) {
    var unloadCityId = $(element).val();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('unloadCityId', unloadCityId, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
    location.reload();
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
 * Shows or hides load preview
 *
 * @param {object} e Event object
 * @param {number} id Load ID
 */
function collapseLoadPreview(e, id) {
    e.preventDefault();

    var td = $('#load-preview-' + id);
    var div = td.find('.content');
    var tr = td.parent();

    if (div.text().length === 0) {
        $.post(actionPreviewLoadInfo, {id: id}, function (content) {
            div.html(content);
            tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
        });
    } else {
        tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
    }
}

/**
 * Shows or hides expired load preview
 *
 * @param {object} e Event object
 * @param {number} id Load ID
 */
function collapseExpiredLoadPreview(e, id) {
    e.preventDefault();
    var credits = $('#credits-amount');
    var td = $('#load-preview-' + id);
    var div = td.find('.content');
    var tr = td.parent();

    if (div.text().length === 0) {
        $.post(actionPreviewExpiredLoadInfo, {id: id}, function (json) {
            div.html(json.content);
            if (credits.length && json['credits'] !== undefined) {
                credits.text(json['credits']);
            }
            if (json['subscription_end_date'] !== undefined) {
                $(document).find('.subscription-end-time').text(json['subscription_end_date']);
                $(document).find('#subscription_end_date').text(json['subscription_end_date'].substr(0, 10));
            }
            if (json['subscription_credits'] !== undefined) {
                $(document).find('.subscription-credits').html(json['subscription_credits']);
            }
            tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
        });
    } else {
        tr.hasClass('hidden') ? tr.removeClass('hidden') : tr.addClass('hidden');
    }
}

/**
 * Shows load link
 *
 * @param {object} e Event object
 * @param {number} id Load ID
 */
function showLoadLink(e, id) {
    e.preventDefault();

    $.pjax({
        type: 'POST',
        url: actionPreviewLoadLink,
        data: {id: id},
        container: '#load-link-preview-pjax',
        push: false,
        replace: false,
        scrollTo: false
    }).done(function () {
        $('#load-link-preview-modal').modal('show');
    });
}

/**
 * Copies load link to clipboard
 */
function copyLoadLinkToClipboard() {
    $('#load-link-field').select();
    if (document.execCommand('Copy')) {
        $("#load-link-success-alert").fadeIn('slow', function() {
            $(this).removeClass('hidden');
        });
        setTimeout(function() {
            $("#load-link-success-alert").fadeOut('slow', function() {
                $(this).addClass('hidden');
            });
        }, 5000); // 5 seconds
    }
}

/**
 *
 * @param data
 */
function renderMapAjax(data) {
    $.pjax({
        type: 'POST',
        url: actionRenderMap + window.location.search,
        container: '#L-T-5',
        cache: false,
        async: true,
        push: false,
    }).done(function () {
        mapRendered = true;
        loadScript();
    });
}

function renderFiltersAjax() {
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
    var loadID = $(e).data('load');
    $.ajax({
        method: 'POST',
        url: actionRenderContactMap,
        data: {load: loadID},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        success: function(data) {
            container.html(data);
            $(e).data('rendered', true);
            $(e).data('map-open', true)
        }
    });
}
$(".hide_current_row").click(function(){$(this).parent().parent().hide();});
function send_mail_userlog(email){
    $.ajax({
        method:"POST",
        url:actionSendMailUserLog,
        data:{email:email},
        contentType:"application/x-www-form-urlencoded; charset=UTF-8",
        success:function(data){
            if(data==1){
                $("#alert-sent-mail").removeClass('hidden').fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
            }
        },
        error:function(error){
            alert("Something went wrong! Please try again later.");
        }
    });
}
function changeSearchFilterIcon(e,n){
    if(n.preventDefault(),mapOpen)
        var a='<i class="fa fa-plus-circle btn-icon map-link"></i> <span class="btn-text map-link">'+TEXT_SHOW_FILTERS+"</span>";
    else 
        var a='<i class="fa fa-minus-circle margin-right-5 btn-icon"></i><span class="btn-text color-black">'+TEXT_HIDE_FILTERS+"</span>";
    $(e).siblings().each(function(e,n){n.remove()}),
    $(e).parent().append(a),mapOpen=!mapOpen
}
$("[data-toggle=popover]").popover({
    html: true,content: function() {
        return $(this).next().html();
    }
});
$(function(){
    $('body').popover({selector: '[data-toggle="popover"]',trigger: 'hover',container:'body',animation:false}).on('hide.bs.popover', function () {
        if ($(".popover:hover").length) {
            return false;
        }
    });
    $('body').on('mouseleave', '.popover', function(){
        $('.popover').popover('hide');
    });
});
function change_email_address(modal_id){
    var user_id = $("input.load_user_id").val();
    var email = $("input.load-user-email-txt-box").val();
    $.ajax({
        type:'POST',
        url:actionChangeEmailAddress,
        data:{email:email,user_id:user_id},
        success:function(data){
            $("#"+modal_id).modal("hide");
            if(data==1){
                $("#email-updated").removeClass('hidden').fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
                $("[data-target=#"+modal_id+"]").html(email);
            }
        },
        error:function(error){
            alert("Something went wrong! Please try again later.");
        }
    });
}
function openEmailModal(email, user_id) {
    $("#change-email-address-modal .load_user_id").val(user_id);
    $("#change-email-address-modal .load-user-email-txt-box").val(email);
    $("#change-email-address-modal").modal("show");
}