/* global timezone, actionSetTimezone, actionDisableSubscriptionAlert */

/**
 * Hides subscription alert message
 *
 * @param {object} e Event object
 * @param {boolean} once if true -> do not send remember choise, just hide once
 */
function hideSubscriptionAlert(e, once) {
    e.preventDefault();
    if (once !== true) {
        $.post(actionDisableSubscriptionAlert, {}, function () {
            $(e.target).parent('.subscription-reminder').fadeOut();
        });
    } else {
        $(e.target).parent('.subscription-reminder').fadeOut();
    }
}

/**
 * Hides subscription alert message
 *
 * @param {object} e Event object
 */
function hideAnnouncementAlert(e) {
    e.preventDefault();
    $.post(actionDisableAnnouncementAlert, {}, function () {
        $('.announcement').fadeOut();
    });
}

/**
 * Executes anonymous functions when documents fully loads
 */
$(document).ready(function () {
    $('.report-bug-sidebar-btn, .report-bug-btn').click(function (e) {
        e.preventDefault();
        showReportBugModal();
    });

    if (timezone === '') {
        var name = getUserTimezone();
        setUserTimezone(name);
    }

    /**
     * Initializes popover
     */
    $('[data-toggle="popover"]').popover({
        trigger: 'hover'
    });

    /**
     * Initializes tooltip
     */
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });

    /**
     * Opens sidebar. If topbar is visible, closes it
     */
    $('#sidebar-toggle').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#sidebar').addClass('toggled');
        $("#menu").removeClass("in");
        $(".toggle-topbar").addClass("collapsed");
    });

    /**
     * Closes sidebar
     */
    $('#close-sidebar, .report-bug-sidebar-btn').click(function (e) {
        e.preventDefault();
        $('#sidebar').removeClass('toggled');
    });

    /**
     * Switches sidebar from mobile version to desktop version
     */
    $(window).resize(function() {
        if ($(this).width() > 1002) {
            // NOTE: 1002 is sidebar collapse width
            $("#sidebar").removeClass("toggled");
        }
    });

    /**
     * On mobile version, when topbar is visible, closes sidebar
     */
    $(".toggle-topbar").click(function(e) {
        e.preventDefault();
        $("#sidebar").removeClass("toggled");
    });

    /**
     * Toggles class if checkbox is checked
     */
    checkedCheckbox();
    $('.custom-checkbox > input').click(function() {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });

    /**
     * Toggles loader before every page load
     */
    $(window).on('beforeunload', function(){
        $('.page-loader').fadeIn('fast');
    });

    /**
     * Fallback for hiding loader, if css property is removed
     */
    $(window).load(function() {
        $('.page-loader').fadeOut('slow');
    });

    //showWhatsNewModal();
    //hideWhatsNewModal();

    // showWhatsNewInDemoModal();
    // hideWhatsNewInDemoModal();
});

/**
 * Toggles class if checkbox is checked
 */
function checkedCheckbox() {
    $('.custom-checkbox > input').each(function() {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
}

/**
 * Returns user timezone name
 */
function getUserTimezone() {
    var timezone = jstz.determine();
    return timezone.name();
}

/**
 * Sets user timezone
 *
 * @param {string} name User timezone name
 */
function setUserTimezone(name) {
    $.post(actionSetTimezone, {name: name});
}

/**
 * Toggles report a bug modal
 */
function showReportBugModal() {
    $('#bug-report-modal').modal('show');
}

/**
 * Creates cookie
 *
 * @param {string} name Cookie name
 * @param {mixed} value Cookie value
 * @param {string} expireDate Cookie expire date
 * @returns bool
 */
function createCookie(name, value, expireDate) {
    document.cookie = name + "=" + value + ";expires=" + expireDate + ";path=/";
}

/**
 * Gets cookie value
 *
 * @param {string} cname
 * @returns {String}
 */
function getCookie(cname) {
    /** @member {string} Cookie name */
    var name = cname + "=";
    /** @member {string} Decoded cookie name */
    var decodedCookie = decodeURIComponent(document.cookie);
    /** @member {array} Cookies array */
    var cookieArray = decodedCookie.split(';');
    for(var i = 0; i < cookieArray.length; i++) {
        /** @member {mixed} Cookie value*/
        var cookie = cookieArray[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) == 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return "";
}

/**
 * Shows modal on page load if cookie is empty
 *
 * @returns response
 */
function showWhatsNewModal() {
    /** @member {number} */
    var cookieValue = getCookie('whats-new-message');
    if (cookieValue === '') {
        $('#whats-new-modal').modal('show');
    }
}

/**
 * Shows whats new in demo modal
 */
function showWhatsNewInDemoModal() {
    var sessionStorageValue = sessionStorage.getItem('whats-new-in-demo-message');
    if (sessionStorageValue === null) {
        // Show whats new in demo modal
        $('#whats-new-in-demo-modal').modal('show');
    }
}

/**
 * Hides modal and sets cookie value on modal close
 *
 * @returns response
 */
function hideWhatsNewModal() {
    $('#whats-new-modal').on('hidden.bs.modal', function () {
        var cookieName = 'whats-new-message';
        var cookieValue = 1;
        var cookieExpireDate = new Date();
        cookieExpireDate.setTime(cookieExpireDate.getTime() + 30 * 60 * 60 * 24 * 1000);
        var expires = cookieExpireDate.toUTCString();
        createCookie(cookieName, cookieValue, expires);
    });
}

/**
 * Adds value to session storage when whats new in demo modal is closed
 */
function hideWhatsNewInDemoModal() {
    $('#whats-new-in-demo-modal').on('hidden.bs.modal', function () {
        sessionStorage.setItem('whats-new-in-demo-message', 'true');
    });
}

/**
 * Hides what new alert message
 *
 * @param {object} e Event object
 */
function dismissModal() {
    var cookieName = 'whats-new-message';
    var cookieValue = 1;
    var cookieExpireDate = new Date();
    cookieExpireDate.setTime(cookieExpireDate.getTime() + 30 * 60 * 60 * 24 * 1000);
    var expires = cookieExpireDate.toUTCString();
    createCookie(cookieName, cookieValue, expires);
}

/**
 * Adds value to session storage to hide whats new in demo modal
 */
function dismissWhatsNewInDemoModal() {
    sessionStorage.setItem('whats-new-in-demo-message', 'true');
}

/**
 * Clears session storage
 */
function clearSessionStorage() {
    sessionStorage.clear();
}
