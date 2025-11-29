jQuery(document).ready(function ($) {

    // Service Alert.
    if ($('.surelywp-sv-notification-alert').length) {
        var surelywp_sv_notification_cookie_count = getCookie('surelywp_sv_notification_count');
        if (null !== surelywp_sv_notification_cookie_count) {
            var notification_db_count = parseInt($('.surelywp-sv-notification-alert').data('notification-count'));
            surelywp_sv_notification_cookie_count = parseInt(surelywp_sv_notification_cookie_count);
            if ( notification_db_count <= surelywp_sv_notification_cookie_count) {
                $('.surelywp-sv-notification-alert').remove();
            }
        }
    }

    $(document).on('click', '.dismiss-notification', function (e) {
        var notification_db_count = parseInt($('.surelywp-sv-notification-alert').data('notification-count'));
        setCookie('surelywp_sv_notification_count', notification_db_count, 1, "/" );
        $('.surelywp-sv-notification-alert').remove();
    });

})