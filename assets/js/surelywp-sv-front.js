"use strict";

jQuery(document).ready(function ($) {

    // add services notification icon.
    if ($('#customer-service-notification-icon').length > 0) {
        var add_notification_icon_interval = setInterval(
            add_notification_icon, 1000
        );
    }

    let external_req_form_submitted = false; // Flag to track form submission
    let sc_buy_event = false;
    $(document).on('click', '.wp-block-surecart-product-buy-button', function (event) {
        if ($('.surelywp-sv-requirement-form').length && !external_req_form_submitted) {
            event.preventDefault(); // Prevent the default click event
            sc_buy_event = event;
            $('.surelywp-sv-requirement-form').submit();
        }
    });

    function add_notification_icon() {
        if ($('sc-tab[aria-label="' + sv_front_ajax_object.service_tab_lable + '"]').length > 0) {
            var notification_icon = $('#customer-service-notification-icon');
            notification_icon.remove();
            notification_icon.removeClass('hidden');
            $('sc-tab[aria-label="' + sv_front_ajax_object.service_tab_lable + '"]').append(notification_icon);
            clearInterval(add_notification_icon_interval);
        }
    }

    // Service Alert.
    if ($('.surelywp-sv-notification-alert').length) {
        var surelywp_sv_notification_cookie_count = getCookie('surelywp_sv_notification_count');
        if (null !== surelywp_sv_notification_cookie_count) {
            var notification_db_count = parseInt($('.surelywp-sv-notification-alert').data('notification-count'));
            surelywp_sv_notification_cookie_count = parseInt(surelywp_sv_notification_cookie_count);
            if (notification_db_count <= surelywp_sv_notification_cookie_count) {
                $('.surelywp-sv-notification-alert').remove();
            }
        }
    }

    $(document).on('click', '.dismiss-notification', function (e) {
        var domain_name = window.location.hostname;
        var notification_db_count = parseInt($('.surelywp-sv-notification-alert').data('notification-count'));
        setCookie('surelywp_sv_notification_count', notification_db_count, 1, "/", domain_name, true); // Sets a cookie with all options
        $('.surelywp-sv-notification-alert').remove();
    });

    FilePond.registerPlugin(

        // encodes the file as base64 data
        FilePondPluginFileEncode,

        // validates the size of the file
        FilePondPluginFileValidateSize,

        // corrects mobile image orientation
        FilePondPluginImageExifOrientation,

        // previews dropped images
        FilePondPluginImagePreview,

        // validates the type of the file
        FilePondPluginFileValidateType

    );

    const revision_msg_filepond = FilePond.create(
        document.querySelector('.revision-msg-filepond'), {
        acceptedFileTypes: sv_front_ajax_object.file_types,
        allowFileEncode: false,
        allowMultiple: true,
        labelIdle: wp.i18n.__('Drag & Drop your files', 'surelywp-services') + ' <span class="filepond--label-action">' + wp.i18n.__('or Browse', 'surelywp-services') + '</span>'
    }
    );

    // create file po
    var service_req_filepond = [];
    setTimeout(function () {

        // create file po
        $('.service-req-file').each(function (index, element) {

            var fileInput = $(element)[0];

            // Extract the number from the name attribute
            var name = $(element).attr('name');
            var number = name.match(/\[(\d+)\]/)[1]; // Extracts the number inside the brackets.

            service_req_filepond[number] = FilePond.create(
                fileInput, {
                acceptedFileTypes: sv_front_ajax_object.file_types,
                allowFileEncode: false,
                allowMultiple: true,
                labelIdle: wp.i18n.__('Drag & Drop your files', 'surelywp-services') + ' <span class="filepond--label-action">' + wp.i18n.__('or Browse', 'surelywp-services') + '</span>'
            });
        });

        $('.filepond--credits').remove();
    }, 500);

    $(document).on('scNextPage scPrevPage', '#sv-user-services-paginate', function (e) {

        $('.services-list-table-card').addClass('loading-cursor');
        $('.service-view-btn').addClass('loading-cursor');
        $('.services-pagination').addClass('loading-cursor');

        var next_page;
        if ('scNextPage' === e.type) {
            next_page = 1;
        } else {
            next_page = 0;
        }

        var current_page = $('sc-pagination').attr('page');
        var db_page_id = $('.services-list-table').data('dashboard-page-id');
        var surecart_db_tab = $('.services-list-table').data('surecart-db-tab');

        current_page = parseInt(current_page);

        $.ajax({
            url: sv_front_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_get_user_service_paginate",
                next_page: next_page,
                current_page: current_page,
                db_page_id: db_page_id,
                surecart_db_tab: surecart_db_tab,
                nonce: sv_front_ajax_object.nonce,
            },
            success: function (res) {

                $('.services-list-table-card').removeClass('loading-cursor');
                $('.service-view-btn').removeClass('loading-cursor');
                $('.services-pagination').removeClass('loading-cursor');

                if (res.success) {

                    $('#services-list-table').html(res.service_table);
                    $('sc-pagination').attr('page', next_page ? ++current_page : --current_page);

                }
            },
        }).catch((error) => {
            $('.services-list-table-card').removeClass('loading-cursor');
            $('.service-view-btn').removeClass('loading-cursor');
            $('.services-pagination').removeClass('loading-cursor');
            console.error("Error:", error);
        });
    });

    $(document).on("scFormSubmit", function (event) {

        // Reqirement Form handler.
        if (event.target.classList[0] == "surelywp-sv-contract-form") {

            event.preventDefault();

            if ($('.alert-msg').length > 0) {
                $('.alert-msg').remove();
            }

            $("#surelywp-sv-contract-form-btn").attr("loading", "true");
            $("#surelywp-sv-contract-form-btn").attr("disabled", "true");

            var nonce = $("#surelywp_sv_contract_form_nonce").val();
            var service_id = $("#surelywp-sv-service-id").val();

            var formData = new FormData();
            formData.append('action', 'surelywp_sv_contract_form_sumbit_callback');
            formData.append('service_id', service_id);
            formData.append('nonce', nonce);

            var jsonData = event.target.getFormJson();
            jsonData
                .then((result) => {

                    for (const [key, value] of Object.entries(result)) {
                        formData.append(key, value);
                    }

                    $.ajax({
                        url: sv_front_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {

                            jQuery('html, body').animate({ scrollTop: 0 }, 800);

                            display_alert(
                                ".services-item-list-heading",
                                res.message,
                                res.success ? "success" : "danger",
                            );
                            $("#surelywp-sv-contract-form-btn").attr("loading", "false");
                            $("#surelywp-sv-contract-form-btn").removeAttr("disabled");

                            setTimeout(function () {
                                window.location.reload();
                            }, 500)
                        },
                    });
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    });

    $(document).on("scFormSubmit", function (event) {

        // Reqirement Form handler.
        if (event.target.classList[0] == "surelywp-sv-requirement-form") {

            event.preventDefault();

            if ($('.alert-msg').length > 0) {
                $('.alert-msg').remove();
            }

            var is_external_req_form = $('#is-external-req-form').val();
            var is_product_page = $('#surelywp-sv-is-product-page').val();

            if (is_external_req_form) {

                // add loading.
                $('.services-requirements-form.external .loader').removeClass('hidden');
                $('.surelywp-sv-requirement-form').css('opacity', '0.5');

                // Submit the form and set the flag
                external_req_form_submitted = true;
            }

            $("#surelywp-sv-requirement-form-btn").attr("loading", "true");
            $("#surelywp-sv-requirement-form-btn").attr("disabled", "true");

            var nonce = $("#surelywp_sv_req_form_submit_nonce").val();
            var service_id = $("#surelywp-sv-service-id").val();
            var service_setting_id = $("#surelywp-sv-service-setting-id").val();
            var product_id = $("#surelywp-sv-product-id").val();

            var formData = new FormData();

            // actions for requirement form handle.
            if (is_external_req_form) {
                formData.append('action', 'surelywp_sv_external_req_form_sumbit_callback');
                formData.append('product_id', product_id);
            } else {
                formData.append('action', 'surelywp_sv_req_form_sumbit_callback');
                formData.append('service_id', service_id);
            }

            formData.append('service_setting_id', service_setting_id);
            formData.append('nonce', nonce);

            var jsonData = event.target.getFormJson();
            jsonData
                .then((result) => {

                    for (const [key, value] of Object.entries(result)) {
                        formData.append(key, value);
                    }

                    // Iterate through each editor instance
                    if ($('.service-requirement-desc').length) {

                        $('.service-requirement-desc').each(function () {

                            var $element = $(this);
                            var editorId = $element.attr('id'); // Get the editor ID
                            var editorName = $element.attr('name'); // Get the editor name

                            // Get content from TinyMCE if initialized, otherwise fallback to textarea value
                            var content = tinyMCE.get(editorId) ? tinyMCE.get(editorId).getContent() : $element.val();

                            formData.append(editorName, content); // Append the content to formData
                        });

                    }

                    // Iterate through each input text
                    if ($('.service-requirement-text').length) {

                        $('.service-requirement-text').each(function () {

                            var $element = $(this);
                            var editorName = $element.attr('name'); // Get the editor name
                            var content = $element.val();

                            formData.append(editorName, content); // Append the content to formData
                        });

                    }

                    // Iterate through each input text
                    if ($('.service-requirement-dropdown').length) {
                        $('.service-requirement-dropdown').each(function () {
                            var $element = $(this);
                            var dropdownName = $element.attr('name'); // Get the dropdown's name attribute
                            var selectedValue = $element.val();       // Get the selected value
                            formData.append(dropdownName, selectedValue); // Append to formData
                        });
                    }

                    if (service_req_filepond.length !== 0) {
                        service_req_filepond.forEach(function (service_req_filepond, index) {
                            if (service_req_filepond !== null) {
                                var number = index;
                                var requirement_files = service_req_filepond.getFiles();

                                $.each(requirement_files, function (index, fileItem) {

                                    if (fileItem.status == 2) { // Status 2 indicates an success 
                                        formData.append('requirement_files[' + number + '][]', fileItem.file);
                                    } else {
                                        service_req_filepond.removeFile(fileItem.id);
                                    }
                                });
                            }
                        });
                    }

                    $.ajax({
                        url: sv_front_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {

                            let alert_class = '';
                            if (is_external_req_form) {
                                if (!is_product_page) {
                                    alert_class = ".services-requirements-form .loader";
                                } else {
                                    alert_class = ".wp-block-surecart-product-title";
                                }
                            } else {
                                alert_class = ".services-item-list-heading";
                            }

                            jQuery('html, body').animate({ scrollTop: 0 }, 800);
                            display_alert(
                                alert_class,
                                res.message,
                                res.success ? "success" : "danger",
                            );

                            $("#surelywp-sv-requirement-form-btn").attr("loading", "false");
                            $("#surelywp-sv-requirement-form-btn").removeAttr("disabled");

                            if (is_external_req_form) {

                                if (res.success) {
                                    if (is_product_page) {
                                        sc_buy_event.target.click();
                                    } else {
                                        $('.surelywp-sv-requirement-form').hide();
                                    }
                                }

                                external_req_form_submitted = false;

                                $('.services-requirements-form.external .loader').addClass('hidden');
                                $('.surelywp-sv-requirement-form').css('opacity', '1');

                            } else if (!is_external_req_form) {
                                setTimeout(function () {
                                    window.location.reload();
                                }, 500)
                            }
                        },
                    });
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    });

    // Display alert message
    function display_alert(location, message, type, closable = 'true') {
        $(location).after(
            "<sc-alert closable=" + closable + " class='alert-msg' open type='" +
            type +
            "'>" +
            message +
            "</sc-alert>"
        );
    }

    // add services list table inside order.
    if ($('.customer-order-services').length > 0) {
        var add_customer_order_services_interval = setInterval(
            add_customer_order_services, 2000
        );
    }

    function add_customer_order_services() {
        var customer_order_services_list = $('.customer-order-services');
        customer_order_services_list.remove();
        $('.sc-customer-dashboard.hydrated').after(customer_order_services_list);
        if ($('.customer-order-services').length > 0) {
            customer_order_services_list.removeClass('hidden');
            clearInterval(add_customer_order_services_interval);
        }
    }

    // handle final delivery approval.

    // Revision the final deivery.
    $(document).on('click', '#delivery-revision-button', function (e) {
        e.preventDefault();
        $('.final-delivery-revision').addClass('show-modal');
    });

    $(document).on('click', '#cancel-delivery-revision', function (e) {
        e.preventDefault();
        $('.final-delivery-revision').removeClass('show-modal');
        if ($('.sv-error').length > 0) {
            $('.sv-error').remove();
        }
    });

    // Handle delivery now form
    $(document).on('click', '#confirm-delivery-revision', function (e) {

        e.preventDefault();
        // remove previous errors.
        if ($('.sv-error').length > 0) {
            $('.sv-error').remove();
        }

        $(".final-delivery-revision.modal .modal-content").animate({ scrollTop: 0 }, "slow");

        // Message Form handler.
        var formData = new FormData();
        var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
        var service_id = $("#surelywp-sv-service-id").val();
        var receiver_id = $("#message-receiver-id").val();
        var service_message = $("#delivery-revision-msg-text").val();
        var service_message = tinyMCE.get('delivery-revision-msg-text') ? tinyMCE.get('delivery-revision-msg-text').getContent() : $('#delivery-revision-msg-text').val();
        if ('' === service_message) {
            service_message = $('#delivery-revision-msg-text').val();
        }
        var is_final_delivery = 0;
        var is_milestone_delivery_flag = 0;
        var milestone_id = $('#milestone-id').val();
        var is_data_milestone_revision = $('#confirm-delivery-revision').attr('data-milestone-revision');
        // Get allow message files
        var msg_attachment_files = revision_msg_filepond.getFiles();
        $.each(msg_attachment_files, function (index, fileItem) {

            if (fileItem.status == 2) { // Status 2 indicates an success 
                formData.append('msg_attachment_file[]', fileItem.file);
            } else {
                revision_msg_filepond.removeFile(fileItem.id);
            }
        });

        if ('' === service_message || undefined === service_message) {
            var error_msg = wp.i18n.__('Please enter your message and try again.', 'surelywp-services');
            $('.delivery-revision-wrap').after('<p class="sv-error">' + error_msg + '</p>');
            return;
        }

        $('.body').addClass('pointer-event-none');
        $('.final-delivery-revision .modal-top .loader').removeClass('hidden');

        formData.append('action', 'surelywp_sv_message_form_sumbit_callback');
        formData.append('service_id', service_id);
        formData.append('receiver_id', receiver_id);
        formData.append('is_final_delivery', is_final_delivery);
        formData.append('is_milestone_delivery_flag', is_milestone_delivery_flag);
        formData.append('is_data_milestone_revision', is_data_milestone_revision);
        formData.append('milestone_id', milestone_id);
        formData.append('nonce', nonce);
        formData.append('service_message', service_message);

        $.ajax({
            url: sv_front_ajax_object.ajax_url,
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {

                $('.body').removeClass('pointer-event-none');
                if (!res.success) {
                    $('.delivery-revision-wrap').after('<p class="sv-error">' + res.message + '</p>');
                } else {
                    var success_message = 'Message sent successfully.';
                    $('.delivery-revision-wrap').after('<p class="sv-success">' + success_message + '</p>');
                    handle_delivery_approval('0');
                }

                if (tinyMCE.get('delivery-revision-msg-text')) {
                    tinyMCE.get('delivery-revision-msg-text').setContent('')
                }

                $('#delivery-revision-msg-text').val('');

                setTimeout(function () {
                    revision_msg_filepond.removeFiles();
                }, 100
                );
            },
        });
    });

    $(document).on('click', '#delivery-approve-button', function (e) {
        e.preventDefault();
        $('.final-delivery-approve').addClass('show-modal');
    });

    // close the modal
    $(document).on('click', '#cancel-delivery-change', function (e) {
        e.preventDefault();
        $('.final-delivery-approve').removeClass('show-modal');
    });

    // approve the final deivery.
    $(document).on('click', '#confirm-approve-delivery', function (e) {
        e.preventDefault();
        $('.modal-top .loader').removeClass('hidden');
        handle_delivery_approval('1');
    });

    function handle_delivery_approval(is_approved) {

        $('body').addClass('pointer-event-none');

        var formData = new FormData();
        var nonce = $('#surelywp_sv_handle_delivery_nonce').val();
        var service_id = $('#service-id').val();
        var service_setting_id = $('#service-setting-id').val();
        var milestone_id = $('#milestone-id').length ? $('#milestone-id').val() : 'no';
        var approve_message_id = $('#approve-message-id').val();

        formData.append('action', 'surelywp_sv_handle_delivery');
        formData.append('nonce', nonce);
        formData.append('service_id', service_id);
        formData.append('service_setting_id', service_setting_id);
        formData.append('milestone_id', milestone_id);
        formData.append('approve_message_id', approve_message_id);
        formData.append('is_approved', is_approved);

        $.ajax({
            url: sv_front_ajax_object.ajax_url,
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {

                if ('0' === is_approved) {
                    $('.final-delivery-revision .loader').addClass('hidden');
                } else {
                    $('.dellvery-approve .dellvery-top .loader').addClass('hidden');
                }
                window.location.reload();
            },
        });

        $('body').removeClass('pointer-event-none');
    }

    $(document).on("scFormSubmit", '#new-service-request-form', function (event) {

        $("#surelywp-sv-request-service-form-submit-btn").attr("loading", "true");
        $("#surelywp-sv-request-service-form-submit-btn").attr("disabled", "true");

        var nonce = $("#surelywp_sv_service_request_form_nonce").val();
        var service_order = $("#service-order-selection").val();
        var dashboard_page_id = $("#dashboard-page-id").val();

        $.ajax({
            url: sv_front_ajax_object.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'surelywp_sv_new_service_req',
                nonce: nonce,
                service_order: service_order,
                dashboard_page_id: dashboard_page_id
            },
            success: function (res) {

                $("#surelywp-sv-request-service-form-submit-btn").attr("loading", "false");
                $("#surelywp-sv-request-service-form-submit-btn").removeAttr("disabled");

                display_alert(
                    ".services-item-list-heading",
                    res.message,
                    res.status ? "success" : "danger",
                    'false'
                );

                if (res.status) {
                    window.location.href = res.redirect_url;
                }
            }
        });
    });

    // Delivery Timer.
    if ($('.service-delivery-panel').length) {

        // For CountDown Timer
        var delivery_date = $('#service-delivery-panel').data('delivery-date') * 1000;
        var current_time = new Date().getTime();

        if (current_time <= delivery_date) {
            // Set the date we're counting down to (replace with your desired end date)
            var countDownDate = delivery_date;

            // Update the countdown every 1 second
            var delivery_timer = setInterval(function () {

                // Get the current date and time
                var now = new Date().getTime();

                // Calculate the remaining time
                var distance = countDownDate - now;

                // Calculate days, hours, minutes, and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // make 2 digit.
                if (hours / 10 <= 1) {
                    hours = hours.toString().padStart(2, '0');
                }

                // make 2 digit.
                if (minutes / 10 <= 1) {
                    minutes = minutes.toString().padStart(2, '0');
                }

                // make 2 digit.
                if (seconds / 10 <= 1) {
                    seconds = seconds.toString().padStart(2, '0');
                }

                if (days / 10 <= 1) {
                    days = days.toString().padStart(2, '0');
                }

                $('.service-delivery-panel .timer .days .count').html(days);
                $('.service-delivery-panel .timer .hours .count').html(hours);
                $('.service-delivery-panel .timer .minutes .count').html(minutes);
                $('.service-delivery-panel .timer .seconds .count').html(seconds);

                // If the countdown is over, display a message
                if (distance <= 0) {

                    clearInterval(delivery_timer);
                    $('.service-delivery-panel .timer .count').html('00');
                }

            }, 1000);
        } else {

            $('.service-delivery-panel .timer .count').html('00');
        }
    }

});