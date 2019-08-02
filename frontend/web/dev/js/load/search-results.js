/* global actionLoadPreview, actionPreviewLoadLink */

/**
 * Executes anonymous function on document ready
 */
$(document).ready(function () {
    registerLoadPreview();
});

/**
 * Registers load preview icon event
 */
function registerLoadPreview() {
    $('.load-preview-icon').click(function (e) {
        e.preventDefault();
        /** @member {number} Load ID */
        var loadId = $(this).data('id');
        loadPreview(loadId);
        $(this).parent().parent().parent().find('.load-expanded-content-' + loadId).toggleClass('in');
    });
}

/**
 * Gets information about load and updates load preview
 *
 * @param {number} loadId Load ID
 */
function loadPreview(loadId) {
    $.post(actionLoadPreview, {
        loadId: loadId
    }, function (response) {
        if (response === '') {
            reloadToastrPjax();
        } else {
            updateLoadPreviewContent(loadId, response);
        }
    });
}

/**
 * Reloads PJAX container with toastr widget inside
 */
function reloadToastrPjax() {
    $.pjax.reload({container: '#search-results-toastr-pjax'});
}

/**
 * Updates load preview content
 *
 * @param {string} content Load information
 */
function updateLoadPreviewContent(loadId, content) {
    $('.expanded-load-preview-content-' + loadId).html(content);
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
function replaceQueryParam(param, value, search) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');

    if (value === '') {
        return query;
    }

    return (query.length > 2 ? query + "&" : "?") + (value ? param + "=" + value : '');
}
function changePageSize(element) {
    var pageSize = $(element).val();
    var pathName = window.location.pathname;
    var queryParams = replaceQueryParam('pageSize', pageSize, window.location.search);
    window.history.pushState(null, '', pathName + queryParams);
    location.reload();
}
function remove_load(load_id) {
    var retVal = confirm("Do you want to remove this load ?");
    if ( retVal == true ) {
        $.ajax({
            method: 'POST',
            url: actionRemoveLoad,
            data: {load_id: load_id},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function(data) {
                if (data == 1) {
                    $("#load-removed").removeClass('hidden').fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
                    $("tr[data-key="+load_id+"]").remove();
                }
            }
        });
    }
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
function openEmailModal(email, user_id) {
    $("#change-email-address-modal .load_user_id").val(user_id);
    $("#change-email-address-modal .load-user-email-txt-box").val($("small#"+user_id).html());
    $("#change-email-address-modal").modal("show");
}
function change_email_address(modal_id){
    var user_id = $("input.load_user_id").val();
    var email = $("input.load-user-email-txt-box").val();
    $.ajax({
        type:'POST',
        url:actionChangeEmailAddress,
        data:{email:email,user_id:user_id},
        success:function(data){
            $("#change-email-address-modal").modal("hide");
            if(data==1){
                $("#email-updated").removeClass('hidden').fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
                $("small#"+user_id).html(email);
            }
        },
        error:function(error){
            alert("Something went wrong! Please try again later.");
        }
    });
}
function activate_load(load_id,user_id) {
    $.ajax({
        type:'POST',
        url:actionActivateLoad,
        data:{load_id:load_id,user_id:user_id},
        success:function(data){
            $("#change-email-address-modal").modal("hide");
            if(data==1){
                $("#log-service-activated").removeClass('hidden').fadeIn().animate({opacity: 1.0}, 4000).fadeOut('slow');
                $("#"+load_id+"_"+user_id).css('color','orange');
            } else {
                alert("Something went wrong! Please try again later.");
            }
        },
        error:function(error){
            alert("Something went wrong! Please try again later.");
        }
    });
}