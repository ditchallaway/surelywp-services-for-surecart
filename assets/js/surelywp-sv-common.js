"use strict";

jQuery(document).ready(function ($) {

    /*
    We want to preview images, so we need to register the Image Preview plugin
    */
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

    // Select the file input and use create() to turn it into a pond
    const messages_file_pond = FilePond.create(
        document.querySelector('.messages-filepond'), {
        acceptedFileTypes: sv_common_ajax_object.file_types,
        allowFileEncode: false,
        allowMultiple: true,
        labelIdle: wp.i18n.__('Drag & Drop your files', 'surelywp-services') + ' <span class="filepond--label-action">' + wp.i18n.__('or Browse', 'surelywp-services') + '</span>'
    }
    );

    // add active class on order service tracker panel.
    if ($('.status-tracker').length) {

        // Find the last checked sc-text element
        var lastChecked = $(".service-track-list sc-text.checked").last();

        // Find the next sc-text element after the last checked one
        var nextScText = lastChecked.next("sc-text");

        // Add the active class to the next sc-text element
        if (nextScText.length) {
            nextScText.addClass("active");
        }
    }

    // Submit form on press enter key.
    $("#service-message-input").keypress(function (event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent the default form submission
            $(".surelywp-sv-message-form").submit(); // Submit the form
        }
    });

    // Call the function initially to scroll to the bottom.
    setTimeout(scrollToBottom, 100);

    var isMessageSendInProgress = false;
    $(document).on("scFormSubmit", function (event) {

        // remove alerts
        if ($('.alert-msg').length > 0) {
            $('.alert-msg').remove();
        }
        // Message Form handler.
        if (event.target.classList[0] == "surelywp-sv-message-form") {

            if (isMessageSendInProgress) {
                return;
            }

            event.preventDefault();
            isMessageSendInProgress = true;
            $("#surelywp-sv-send-message-btn").attr("loading", "true");
            $("#surelywp-sv-send-message-btn").attr("disabled", "true");

            var formData = new FormData();

            var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
            var service_id = $("#surelywp-sv-service-id").val();
            var service_setting_id = $("#surelywp-sv-service-setting-id").val();
            var receiver_id = $("#message-receiver-id").val();
            var is_final_delivery = 0;
            var is_milestone_delivery_flag = 0;
            var is_final_delivery = $('#is-final-delivery').is(':checked') ? 1 : 0;
            // var is_milestone_delivery_sent = $('#is-milestone-delivery').is(':checked') ? 1 : 0;
            var is_milestone_delivery = $('#is-final-delivery').attr('data-milestone');
            
            if (parseInt(is_milestone_delivery) !== 0) {
                is_milestone_delivery_flag = 1;
            } else {
                is_milestone_delivery_flag = 0;
            }
            // Get allow message files
            var msg_attachment_files = messages_file_pond.getFiles();
            $.each(msg_attachment_files, function (index, fileItem) {

                if (fileItem.status == 2) { // Status 2 indicates an success 
                    formData.append('msg_attachment_file[]', fileItem.file);
                } else {
                    messages_file_pond.removeFile(fileItem.id);
                }
            });

            formData.append('action', 'surelywp_sv_message_form_sumbit_callback');
            formData.append('service_id', service_id);
            formData.append('service_setting_id', service_setting_id);
            formData.append('receiver_id', receiver_id);
            formData.append('is_final_delivery', is_final_delivery);
            formData.append('is_milestone_delivery_flag', is_milestone_delivery_flag);
            formData.append('is_milestone_delivery', is_milestone_delivery);
            // formData.append('is_milestone_delivery_sent', is_milestone_delivery);
            formData.append('nonce', nonce);

            var jsonData = event.target.getFormJson();
            jsonData
                .then((result) => {

                    var service_message = tinyMCE.get('service-message-input') ? tinyMCE.get('service-message-input').getContent() : $('#service-message-input').val();
                    if ('' === service_message) {
                        service_message = $('#service-message-input').val();
                    }

                    if ('' == service_message) {
                        display_alert(
                            ".services-tabs",
                            wp.i18n.__('Please enter your message and try again.', 'surelywp-services'),
                            "danger",
                        );
                        jQuery('html, body').animate({ scrollTop: 0 }, 800);
                        $("#surelywp-sv-send-message-btn").attr("loading", "false");
                        $("#surelywp-sv-send-message-btn").removeAttr("disabled");
                        isMessageSendInProgress = false;
                        return;
                    }

                    formData.append('service_message', service_message);

                    $.ajax({
                        url: sv_common_ajax_object.ajax_url,
                        type: "POST",
                        dataType: "json",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {

                            // Reload page if it is final delivery.
                            if (is_final_delivery) {
                                send_service_message_mail(res.message_data);
                                window.location.reload();
                            } else {

                                if (!res.success) {

                                    display_alert(
                                        ".services-tabs",
                                        res.message,
                                        "danger",
                                    );
                                }

                                scrollToBottom();

                                if (tinyMCE.get('service-message-input')) {
                                    tinyMCE.get('service-message-input').setContent('')
                                }

                                $('#service-message-input').val('');

                                setTimeout(function () {
                                    messages_file_pond.removeFiles();
                                }, 100
                                );

                                isMessageSendInProgress = false;
                                $("#surelywp-sv-send-message-btn").attr("loading", "false");
                                $("#surelywp-sv-send-message-btn").removeAttr("disabled");

                                // Send mail for message.
                                send_service_message_mail(res.message_data);
                            }
                        },
                    });
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    });


    function send_service_message_mail(message_data) {

        var nonce = $("#surelywp_sv_message_form_submit_nonce").val();

        $.ajax({
            url: sv_common_ajax_object.ajax_url,
            type: "POST",
            dataType: "json",
            data: {
                action: 'surelywp_sv_send_message_mail',
                nonce: nonce,
                message_data: message_data
            },
        });
    }

    // For admin set interval for fetch messages and check delivery status.
    if ($('.surelywp-sv-messages.admin').length > 0) {

        var service_status = $('#surelywp-sv-service-status').val();

        if ('service_submit' === service_status) {

            var check_delivery_status_interval = setInterval(
                check_delivery_status, // set interval for check delivery approve status after final delivery.
                2500
            );

        }

        var fetch_service_messages_interval = setInterval(
            fetch_service_messages,
            3000
        );

    } else if ($('.surelywp-sv-messages.customer').length > 0) { // For Customer set interval for fetch messages.
        var fetch_service_messages_interval = setInterval(
            fetch_service_messages,
            3000
        );
    }

    // get service message.
    function fetch_service_messages() {

        var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
        var service_id = $("#surelywp-sv-service-id").val();
        var last_message_datatime = $('.surelywp-sv-msg:last').find('p.complete-datetime').text();
        $.ajax({
            url: sv_common_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_fetch_service_messages",
                service_id: service_id,
                last_message_datatime: last_message_datatime,
                nonce: nonce,
            },
            success: function (res) {

                if (res.success) {

                    // reload screen for final delivery message. 
                    if (res.is_final_delivery) {
                        window.location.reload();
                        clearInterval(fetch_service_messages_interval);
                    } else {

                        last_message_datatime = $('.surelywp-sv-msg:last').find('p.complete-datetime').text();

                        if (res.message_time > last_message_datatime) {

                            // Select the .surelywp-sv-messages container
                            var messagesContainer = $('.surelywp-sv-messages');

                            // Check if the container has any .surelywp-sv-msg elements
                            var lastMsg = messagesContainer.find('.surelywp-sv-msg:last');

                            if (lastMsg.length > 0) {

                                if (lastMsg.next('.delivery-message.bottom').length) {
                                    lastMsg.next('.delivery-message.bottom').after(res.message_html);
                                } else {
                                    // If lastMsg exists, append HTML after it
                                    lastMsg.after(res.message_html);
                                }
                            } else {
                                // If lastMsg does not exist, append the new .surelywp-sv-msg element to the container
                                messagesContainer.append(res.message_html);

                            }
                            scrollToBottom();
                        }
                    }
                }
            },
        }).catch((error) => {
            console.error("Error:", error);
        });
    }


    // Check final delivery status
    function check_delivery_status() {

        var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
        var service_id = $("#surelywp-sv-service-id").val();
        var final_delivery_note = $('.delivery-message.top:last');
        var final_delivery_msg = final_delivery_note.next('.surelywp-sv-msg:first');
        var message_id = final_delivery_msg.data('message-id');

        $.ajax({
            url: sv_common_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_check_delivery_status",
                service_id: service_id,
                message_id: message_id,
                nonce: nonce,
            },
            success: function (res) {

                if (res.success && res.is_status_update) {

                    window.location.reload();
                    clearInterval(check_delivery_status_interval);
                }
            },
        }).catch((error) => {
            console.error("Error:", error);
        });
    }

    // Function to scroll to the bottom of the messages div
    function scrollToBottom() {
        var messagesDiv = $('.surelywp-sv-messages');
        if (messagesDiv.length) {
            messagesDiv.scrollTop(messagesDiv.prop("scrollHeight"));
        }
    }

    // Function to check if scrolled to the top
    function get_messages_div_height() {
        var messagesDiv = $('.surelywp-sv-messages');
        return messagesDiv.scrollTop();
    }

    var no_more_messages = false;
    // Event listener for scroll
    $('.surelywp-sv-messages').scroll(function () {

        var messages_div_height = get_messages_div_height();

        var messagesContainer = $('.surelywp-sv-messages');

        if (messages_div_height === 0) {
            messagesContainer.scrollTop(2); // set Scroll 2px down. 
        }

        // if no more messages is not set and messages scroll on top and loader is not present.
        if ((!no_more_messages && messages_div_height === 0 && $('#load-more-loader').length == 0)) {

            messagesContainer.prepend('<sc-spinner class="load-more-loader" id="load-more-loader"></sc-spinner>');

            var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
            var service_id = $("#surelywp-sv-service-id").val();
            var first_message_datatime = $('.surelywp-sv-msg:first').find('p.complete-datetime').text();
            $.ajax({
                url: sv_common_ajax_object.ajax_url,
                dataType: "json",
                type: "POST",
                data: {
                    action: "surelywp_sv_load_more_service_messages",
                    nonce: nonce,
                    service_id: service_id,
                    first_message_datatime: first_message_datatime,
                },
                success: function (res) {

                    $('#load-more-loader').remove();
                    if (res.success) {

                        messagesContainer.prepend(res.message_html);
                    } else {
                        no_more_messages = true;
                    }
                },
            }).catch((error) => {
                $('#load-more-loader').remove();
                console.error("Error:", error);
            });

        }
    });

    // Display alert message
    function display_alert(location, message, type) {
        $(location).after(
            "<sc-alert closable='true' duration=2000 class='alert-msg' open type='" +
            type +
            "'>" +
            message +
            "</sc-alert>"
        );
    }

    // Service Tab switching
    $(document).on('click', '.services-tabs ul li', function (e) {

        e.preventDefault();

        // Remove the active class from all tabs
        $('.services-tabs ul li').removeClass('active');

        // Add the active class to the clicked tab
        $(this).addClass('active');

        // Hide all tab contents
        $('.services-chats-wrap, .services-activity-wrap, .services-contract-wrap, .services-requirements-wrap, .services-delivery-wrap').addClass('hidden');

        // Determine which tab was clicked and show its content
        var tabClass = $(this).attr('class');
        if (tabClass.includes('messages')) {
            $('.services-chats-wrap').removeClass('hidden');
        } else if (tabClass.includes('activity')) {
            $('.services-activity-wrap').removeClass('hidden');
        } else if (tabClass.includes('contract')) {
            $('.services-contract-wrap').removeClass('hidden');
        } else if (tabClass.includes('requirements')) {
            $('.services-requirements-wrap').removeClass('hidden');
        } else if (tabClass.includes('delivery')) {
            $('.services-delivery-wrap').removeClass('hidden');
        }
    });

    // for view services link.
    $(document).on('click', '#service-requirements-view', function (e) {
        e.preventDefault();

        // Remove the active class from all tabs
        $('.services-tabs ul li').removeClass('active');

        // Add the active class to the clicked tab
        $('.services-tabs ul li.requirements').addClass('active');

        // Hide all tab contents
        $('.services-chats-wrap, .services-activity-wrap').addClass('hidden');
        $('.services-requirements-wrap').removeClass('hidden');

    });


    // close model
    $(document).on('click', '.close-button', function (e) {
        $(this).closest('.surelywp-sv-modal').find('.modal').removeClass('show-modal');
    });

});