"use strict";

jQuery(document).ready(function ($) {

    // Toggle Requrements.
    $(document).on('click', '.req-close-icon, .req-field-label-top.close', function (e) {
        $(this).closest('.service-requirements-field').find('.req-open-icon').removeClass('hidden');
        $(this).closest('.service-requirements-field').find('.req-close-icon').addClass('hidden');
        $(this).closest('.service-requirements-field').find('.sv-requirements-options').slideUp();
        $(this).closest('.service-requirements-field').find('.req-field-label-top').addClass('open').removeClass('close');
        $(this).closest('.service-requirements-field').removeClass('open');
    });

    $(document).on('click', '.req-open-icon, .req-field-label-top.open', function (e) {
        $(this).closest('.service-requirements-field').find('.req-open-icon').addClass('hidden');
        $(this).closest('.service-requirements-field').find('.req-close-icon').removeClass('hidden');
        $(this).closest('.service-requirements-field').find('.sv-requirements-options').slideDown();
        $(this).closest('.service-requirements-field').find('.req-field-label-top').addClass('close').removeClass('open');
        $(this).closest('.service-requirements-field').addClass('open');

    });

    // Toggle Requrements.
    $(document).on('click', '.mil-close-icon, .mil-field-label-top.close', function (e) {
        $(this).closest('.service-milestones-field').find('.mil-open-icon').removeClass('hidden');
        $(this).closest('.service-milestones-field').find('.mil-close-icon').addClass('hidden');
        $(this).closest('.service-milestones-field').find('.sv-milestones-options').slideUp();
        $(this).closest('.service-milestones-field').find('.mil-field-label-top').addClass('open').removeClass('close');
        $(this).closest('.service-milestones-field').removeClass('open');
    });

    $(document).on('click', '.mil-open-icon, .mil-field-label-top.open', function (e) {
        $(this).closest('.service-milestones-field').find('.mil-open-icon').addClass('hidden');
        $(this).closest('.service-milestones-field').find('.mil-close-icon').removeClass('hidden');
        $(this).closest('.service-milestones-field').find('.sv-milestones-options').slideDown();
        $(this).closest('.service-milestones-field').find('.mil-field-label-top').addClass('close').removeClass('open');
        $(this).closest('.service-milestones-field').addClass('open');

    });

    // Add product service enable block on surecart product page.
    if ($('#product-service-block').length > 0) {
        var product_service_block = $('#product-service-block');
        product_service_block.remove();
        $('.css-wzxb7d > div').eq(0).after(product_service_block);
    }

    // Add Customer Services on Surecart Customer View page.
    if ($('#customer-services-list-block').length > 0) {

        var customer_service_block = $('#customer-services-list-block');
        customer_service_block.removeClass('hidden');
        $('.css-1m2qrki').append(customer_service_block);
    }

    // Toggle Recurring Services Options.
    $(document).on('change', '#surelywp-sv-is-enable-recurring-services', function (e) {
        $('#surelywp-sv-recurring-services-settings-options').toggle();
        $('#surelywp-sv-number-of-allow-sv-per-order-option').toggle();
    });

    // Toggle Display Req form options.
    $(document).on('change', '#is-display-req-form-on-product-page', function (e) {
        $('#surelywp-sv-req-form-options').toggle();
    });

    // Toggle Display Revision Milestone options.
    $(document).on('change', '#milestones-revision-allowed', function () {
        $(this).closest('.sv-milestones-options').find('#surelywp-sv-revision-number-options').toggle();
    });

    $(document).on("click", ".service-block-previous, .service-block-next", function (e) {

        var buttons = $(".service-block-previous, .service-block-next");
        buttons.attr('loading', true);
        $('#customer-services-skeleton').removeClass('hidden');
        $('#customer-services-list-table').addClass('hidden');

        var class_list = e.target.classList.value;
        var block_data = $('#customer-services-pagination-data');
        var current_page = parseInt(block_data.data('current-page'));
        var total_pages = parseInt(block_data.data('total-pages'));
        var user_id = parseInt(block_data.data('user-id'));
        var next_page;

        if (class_list.includes('service-block-next')) {
            next_page = current_page + 1;
        } else {
            next_page = current_page - 1;
        }

        $.ajax({
            url: sv_backend_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_get_user_service_block_paginate",
                user_id: user_id,
                next_page: next_page,
                nonce: sv_backend_ajax_object.nonce,
            },
            success: function (res) {

                buttons.attr('loading', false);
                $('#customer-services-skeleton').addClass('hidden');
                $('#customer-services-list-table').removeClass('hidden');

                if (res.success) {

                    $('.customer-services-admin-block').html(res.service_table);
                    block_data.data('current-page', next_page);

                    // For Next button.
                    if (next_page === total_pages) {
                        $('.service-block-next').attr('disabled', true);
                    } else if (1 === next_page) {
                        $('.service-block-next').attr('disabled', false);
                    }

                    // For Previous Button.
                    if (1 === next_page) {
                        $('.service-block-previous').attr('disabled', true);
                    } else {
                        $('.service-block-previous').attr('disabled', false);
                    }
                }
            },
        }).catch((error) => {
            console.error("Error:", error);
        });
    });

    $(document).on("scChange", function (event) {

        var classes = event.target.classList.value;

        if (classes.includes("surelywp-product-service-enable-switch")) {

            if (event.target.checked) {
                $('.surelywp-services-selection-wrap').removeClass('hidden');
                var service_setting_id = $('#surelywp-services-selection').val();
                manage_product_service(1, service_setting_id);
            } else {
                $('.surelywp-services-selection-wrap').addClass('hidden');
                manage_product_service(0);
            }

        } else if (classes.includes("surelywp-services-selection")) {

            var service_setting_id = $('#surelywp-services-selection').val();
            manage_product_service(1, service_setting_id);
        }
    });

    function manage_product_service(is_service_enable, service_setting_id = '') {

        var nonce = $('#surelywp_sv_manage_product_service_nonce').val();
        var product_id = $('#sv-product-id').val();
        var current_service_setting_id = $('#current-service-setting-id').val();

        $.ajax({
            url: sv_backend_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_manage_product_service",
                nonce: nonce,
                product_id: product_id,
                is_service_enable: is_service_enable,
                service_setting_id: service_setting_id,
                current_service_setting_id: current_service_setting_id,
            },
            success: function (res) {
                $('#current-service-setting-id').val(service_setting_id);
                $('.css-uksis0').append('<div class="sv-product-updated" style="height: auto; opacity: 1;"><div class="components-snackbar-list__notice-container"><div class="components-snackbar" tabindex="0" role="button" aria-label="Dismiss this notice"><div class="components-snackbar__content">Product updated.</div></div></div></div>');
                setTimeout(function () {
                    $('.sv-product-updated').remove();
                }, 2000
                );
            },
        }).catch((error) => {
            console.error("Error:", error);
        });
    }

    // Remove pending for admin service view.
    if ($('.surelywp-surecart-header').length) {
        $('#wpcontent').attr('style', 'padding-left: 0px !important');
    }

    // Remove disable after page load.
    $('#services-settings-form').removeClass('cursor-not-allow');

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

    const delivery_now_filepond = FilePond.create(
        document.querySelector('.delivery-now-filepond'), {
        acceptedFileTypes: sv_backend_ajax_object.file_types,
        allowFileEncode: false,
        allowMultiple: true,
        labelIdle: wp.i18n.__('Drag & Drop your files', 'surelywp-services') + ' <span class="filepond--label-action">' + wp.i18n.__('or Browse', 'surelywp-services') + '</span>'
    }
    );

    function displaySelection(selectedOption) {

        if (selectedOption == "all") {
            $("#specific-product-selection-div").hide();
            $("#specific-product-collection-selection-div").hide();
        } else if (selectedOption == "specific") {
            $("#specific-product-collection-selection-div").hide();
            $("#specific-product-selection-div").show();
        } else if (selectedOption == "specific_collection") {
            $("#specific-product-collection-selection-div").show();
            $("#specific-product-selection-div").hide();
        }
    }

    // For toggle Auto Complete Orders.
    $(document).on("change", "#is-enable-auto-order-complete", function () {
        $('#auto-complete-option').toggle();
    });

    $(document).on("change", "#is-enable-allow-file-uploads", function () {
        $('#file-type-option').toggle();
        $('#add-mime-type-option').toggle();
        $('#file-size-option').toggle();
    });

    // For change Number Of Services Allowed Based On
    $(document).on("change", "#recurring-base-on-options", function () {

        let recurring_base_on = $(this).val();
        if ('subscription_cycle' == recurring_base_on) {
            $('#custom-frequency-options').hide();
        } else if ('custom_frequency' == recurring_base_on) {
            $('#custom-frequency-options').show();
        }

    });

    // For services Product selection
    $(document).on("change", "#services-product-type", function () {
        displaySelection($(this).val());
    });

    // For change Requirement Field Type
    $(document).on("change", ".req-field-type", function () {

        var file_type = $(this).val();

        if ('textarea' === file_type) {

            $(this).closest('.service-requirements-field').find('.note').addClass('hidden');
            $(this).closest('.service-requirements-field').find('.file-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.textarea-required-label').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.is-enable-rich-text-editor-option').removeClass('hidden');
            
            $(this).closest('.service-requirements-field').find('.dropdown-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.req-input-dropdown').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.dropdown-add-btn').addClass('hidden-label');

        } else if ('text' === file_type) {
            $(this).closest('.service-requirements-field').find('.note').addClass('hidden');
            $(this).closest('.service-requirements-field').find('.file-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.textarea-required-label').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.is-enable-rich-text-editor-option').addClass('hidden');

            $(this).closest('.service-requirements-field').find('.dropdown-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.req-input-dropdown').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.dropdown-add-btn').addClass('hidden-label');

        } else if ('file' === file_type) {
            $(this).closest('.service-requirements-field').find('.note').removeClass('hidden');
            $(this).closest('.service-requirements-field').find('.textarea-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.file-required-label').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.is-enable-rich-text-editor-option').addClass('hidden');

            $(this).closest('.service-requirements-field').find('.dropdown-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.req-input-dropdown').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.dropdown-add-btn').addClass('hidden-label');
        } else if ('dropdown' === file_type) { 
            $(this).closest('.service-requirements-field').find('.note').addClass('hidden');
            $(this).closest('.service-requirements-field').find('.textarea-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.file-required-label').addClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.is-enable-rich-text-editor-option').addClass('hidden');
            $(this).closest('.service-requirements-field').find('.dropdown-required-label').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.req-input-label-dropdown').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.req-input-dropdown').removeClass('hidden-label');
            $(this).closest('.service-requirements-field').find('.dropdown-add-btn').removeClass('hidden-label');
        }
    });

    // For Enable services Product
    $(document).on("change", "#surelywp-sv-settings-status", function () {

        if ($(this).is(":checked")) {
            // $("#surelywp-sv-recurring-services-settings").show();
            // $("#surelywp-sv-product-selection-settings").show();
            // $('#surelywp-sv-requirement-settings').show();
            $('#surelywp-sv-order-again-settings').show();
            // $('#surelywp-sv-delivery-settings').show();
            // $('#surelywp-sv-contract-settings').show();
            // $('#services-product-type').trigger('change');
            // $('#ask-for-req').trigger('change');
            // $('#ask-for-contract').trigger('change');

        } else {
            // $("#surelywp-sv-recurring-services-settings").hide();
            // $("#surelywp-sv-product-selection-settings").hide();
            // $('#surelywp-sv-requirement-settings').hide();
            $('#surelywp-sv-order-again-settings').hide();
            // $('#surelywp-sv-delivery-settings').hide();
            // $('#surelywp-sv-contract-settings').hide();
            // $('#services-product-type').trigger('change');
            // $('#ask-for-req').trigger('change');
            // $('#ask-for-contract').trigger('change');
        }
    });


    // For Requirements Fields
    $(document).on("change", "#ask-for-req", function () {

        if ($(this).is(":checked")) {
            $("#service-requirements-fields").show();
        } else {
            $("#service-requirements-fields").hide();

        }
    });

    // For Contract Fields
    $(document).on("change", "#ask-for-contract", function () {

        if ($(this).is(":checked")) {
            $("#service-contract-fields").show();
        } else {
            $("#service-contract-fields").hide();

        }
    });

    if ($('.order-services-list-block').length > 0) {
        var order_service_list_block = $('.order-services-list-block');
        order_service_list_block.remove();
        $('.css-1m2qrki').append(order_service_list_block);
    }

    if ($('#sv-add-mime-types').length) {

        $('#sv-add-mime-types').select2(
            {
                tags: true,
                tokenSeparators: [',', ' '],
            }
        );


        // Mime validate Validation
        $('#sv-add-mime-types').on('select2:selecting', function (e) {

            // Validate mime format before allowing it to be added
            var isValidMime = isValidMimeType(e.params.args.data.text);
            if (!isValidMime) {
                e.preventDefault();
            }
        });

        function isValidMimeType(mimeType) {

            // Regular expression to match a valid MIME type (e.g., "type/subtype")
            var pattern = /^[a-zA-Z0-9]+\/[a-zA-Z0-9\.\-\+]+$/;

            return pattern.test(mimeType);
        }
    }


    if ($('#sv-recipient-email').length) {

        // For make muliple enter multiple email address.
        $('#sv-recipient-email').select2({
            tags: true,
            tokenSeparators: [',', ' '],
        });

        // Recipient Email Validation
        $('#sv-recipient-email').on('select2:selecting', function (e) {
            // Validate email format before allowing it to be added
            var isValidEmail = isValidEmailAddress(e.params.args.data.text);
            if (!isValidEmail) {
                e.preventDefault();
            }
        });

        function isValidEmailAddress(emailAddress) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailAddress);
        }
    }

    // Remove Dropdown Field.
    jQuery(document).on('click', '.service-requirements-dropdown-field-remove', function (e) {
        e.preventDefault();

        var $btn      = jQuery(this);
        var $dropdown = $btn.closest('.req-input-dropdown');

        if (!$dropdown.length) return;

        // find the outer container (the .form-control that contains dropdowns + the add button)
        var $container = $dropdown.closest('.form-control');

        // remove the clicked dropdown
        $dropdown.remove();

        // reindex remaining dropdown inputs inside this same requirement/container
        var $inputs = $container.find('input.req-input-dropdown-options');

        $inputs.each(function (index) {
            var $inp = jQuery(this);
            var name = $inp.attr('name') || '';

            // If name ends with [number], replace that final number with the new index.
            // Otherwise, fallback to appending the index in brackets.
            var m = name.match(/\[\d+\]$/);
            if (m) {
                var base = name.replace(/\[\d+\]$/, '');
                $inp.attr('name', base + '[' + index + ']');
            } else {
                // best-effort fallback — adapt if your actual names differ
                $inp.attr('name', name + '[' + index + ']');
            }
        });

        // Update the hidden dropdown counter inside this requirement if it exists.
        var newCount = Math.max(0, $inputs.length - 1);
        var $counter = $container.find('#dropdown-fields-count');
        if ($counter.length) {
            $counter.val(newCount);
        }
    });

    $(document).on('click', '#add-new-milestone-btn', function (e) {
        e.preventDefault();

        var new_field = $('.service-milestones-field:first').clone();
        var service_setting_id = $('#surelywp-sv-service-setting-id').val();
        var milestone_fields_count = Number($('#milestone-fields-count').val()) + 1;

        // Reset field top and toggle
        new_field.find('.service-milestones-field-remove').removeClass('hidden');
        new_field.find('.mil-field-label-top').html('');

        new_field.find('.mil-open-icon').addClass('hidden');
        new_field.find('.surelywp-sv-revision-number-options').addClass('hidden');
        new_field.find('.mil-close-icon').removeClass('hidden');
        new_field.find('.sv-milestones-options').css('display', 'block');

        // Reset inputs
        new_field.find('input[type="text"], input[type="number"], textarea').val('');
        new_field.find('input[type="checkbox"]').prop('checked', false);

        // Update field names with new milestone index
        var field_key = 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + milestone_fields_count + ']';

        new_field.find('.milestones-field-label').attr('name', field_key + '[milestones_field_label]');
        new_field.find('input[name*="[milestones_require_approval]"]').attr('name', field_key + '[milestones_require_approval]');
        new_field.find('input[name*="[milestones_revision_allowed]"]').attr('name', field_key + '[milestones_revision_allowed]');
        new_field.find('input[name*="[milestone_revisions_number]"]').attr('name', field_key + '[milestone_revisions_number]');
        new_field.find('input[name*="[milestone_delivery_days]"]').attr('name', field_key + '[milestone_delivery_days]');

        // Update hidden milestone counter
        $('#milestone-fields-count').val(milestone_fields_count);

        // Append the new milestone after last one
        $('.service-milestones-field:last').after(new_field);
    });

    // Requirement Repeater.
    $(document).on('click', '#add-new-req-btn', function (e) {

        e.preventDefault();
        var new_field = $('.service-requirements-field:first').clone();
        var service_setting_id = $('#surelywp-sv-service-setting-id').val();
        var req_fields_count = Number($('#req-fields-count').val()) + 1;
        var editor_id = 'sv-requirement-' + req_fields_count;

        new_field.find('.service-requirements-field-remove').removeClass('hidden');
        new_field.find('input[type="text"], textarea').val('');
        new_field.find('.req-field-label-top').html('');
        new_field.find('.req-field-label-top').html('');

        new_field.find('.req-open-icon').addClass('hidden');
        new_field.find('.req-close-icon').removeClass('hidden');
        new_field.find('.sv-requirements-options').css('display', 'block');

        // Make Default Required file uplode field.
        let is_required_field = new_field.find('.is-require-field');
        is_required_field.prop('checked', true).trigger('change');
        new_field.find('.is-enable-rich-text-editor-option').addClass('hidden');

        var field_key = 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][req_fields][' + req_fields_count + ']';

        new_field.find('.req-field-label').attr('name', field_key + '[req_field_label]');
        new_field.find('.req-field-type').attr('name', field_key + '[req_field_type]');
        new_field.find('.is-require-field').attr('name', field_key + '[is_required_field]');
        new_field.find('.req-input-dropdown-options').attr('name', field_key + '[req_field_dropdown_options]');
        new_field.find('.req-title').attr('name', field_key + '[req_title]');
        new_field.find('.req-desc').attr('name', field_key + '[req_desc]');
        new_field.find('.is-enable-rich-text-editor').attr('name', field_key + '[is_enable_rich_text_editor]');

        // --- Reset dropdowns ---
        new_field.find('.req-input-dropdown').remove(); // remove all cloned dropdowns

        // build fresh dropdown
        var freshDropdown = $(
            '<div class="req-input-dropdown">' +
                '<input type="text" class="req-input-dropdown-options widefat" ' +
                    'name="' + field_key + '[req_field_dropdown_options][0]" value="">' +
                '<div class="field-actions">' +
                    '<div class="dropdown-field-drag-handle">' +
                        '<img src="'+ sv_backend_ajax_object.image_path +'/drag-icon.svg" alt="drag">' +
                    '</div>' +
                    '<div class="service-requirements-dropdown-field-remove">' +
                        '<img src="'+ sv_backend_ajax_object.image_path +'remove-dropdown-Icon.svg" alt="close">' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        // insert dropdown before the add button wrapper
        new_field.find('.dropdown-add-btn').before(freshDropdown);

        // reset dropdown counter
        new_field.find('#dropdown-fields-count').val(0);


        $('#req-fields-count').val(req_fields_count);

        $('.service-requirements-field:last').after(new_field);

        // Remove existing editor.
        new_field.find('.wp-editor-wrap').remove();

        // add Editor.
        addEditor(new_field, editor_id, field_key);
    });

    // Dropdown Repeater in Requirement Field.
    $(document).on('click', '.add-new-dropdown-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);

        // 1) Find the closest ancestor that actually contains req-input-dropdown elements.
        //    This avoids accidentally selecting the add-button wrapper itself (which also has .form-control).
        var $requirementWrapper = $btn.closest('.form-control');
        if (!$requirementWrapper.find('.req-input-dropdown').length) {
            $requirementWrapper = $btn.parents().filter(function () {
                return $(this).find('.req-input-dropdown').length;
            }).first();
        }

        // If still not found, fallback to the add-button's immediate parent
        if (!$requirementWrapper || !$requirementWrapper.length) {
            $requirementWrapper = $btn.closest('div');
        }

        // 2) Determine a template to clone (either the last dropdown inside this requirement
        //    or a global one; if none exist, create a tiny fallback DOM structure)
        var $lastDropdown = $requirementWrapper.find('.req-input-dropdown').last();
        var new_field;
        if ($lastDropdown.length) {
            new_field = $lastDropdown.clone(true, true);
        } else {
            var $global = $('.req-input-dropdown').first();
            if ($global.length) {
                new_field = $global.clone(true, true);
            } else {
                // minimal fallback structure (you can extend markup to match your UI)
                new_field = $(
                    '<div class="req-input-dropdown">' +
                        '<input type="text" class="req-input-dropdown-options widefat" value="">' +
                        '<div class="field-actions">' +
                            '<div class="dropdown-field-drag-handle"></div>' +
                            '<div class="service-requirements-dropdown-field-remove"></div>' +
                        '</div>' +
                    '</div>'
                );
            }
        }

        // 3) Service ID (may be empty string if not present)
        var service_setting_id = $('#surelywp-sv-service-setting-id').val() || '';

        // 4) Find the req_fields index by scanning any input[name] in this requirement wrapper.
        //    Use a proper escaped regex: /\[req_fields\]\[(\d+)\]/
        var req_fields_count = 0; // default
        $requirementWrapper.find('input[name]').each(function () {
            var n = $(this).attr('name') || '';
            var m = n.match(/\[req_fields\]\[(\d+)\]/);
            if (m) {
                req_fields_count = parseInt(m[1], 10);
                return false; // break .each
            }
        });

        // 5) Find the highest existing dropdown index in this requirement (if any)
        var lastIndex = -1;
        $requirementWrapper.find('input.req-input-dropdown-options[name]').each(function () {
            var n = $(this).attr('name') || '';
            var mm = n.match(/\[req_field_dropdown_options\]\[(\d+)\]/);
            if (mm) {
                var idx = parseInt(mm[1], 10);
                if (!isNaN(idx)) lastIndex = Math.max(lastIndex, idx);
            }
        });
        var newIndex = lastIndex + 1; // if lastIndex = -1 => newIndex = 0

        // 6) Reset clone values and set new name attribute
        new_field.find('input.req-input-dropdown-options').val('');
        var field_key =
            'surelywp_sv_settings_options[surelywp_sv_settings_options][' +
            service_setting_id +
            '][req_fields][' +
            req_fields_count +
            '][req_field_dropdown_options][' +
            newIndex +
            ']';
        new_field.find('input.req-input-dropdown-options').attr('name', field_key);

        // show remove button if present
        new_field.find('.service-requirements-dropdown-field-remove').removeClass('hidden');

        // 7) Insert into DOM: after last dropdown if exists, otherwise before add-button wrapper (so it's still inside the requirement)
        if ($lastDropdown.length) {
            $lastDropdown.after(new_field);
        } else {
            // If wrapper contains only the add-button, insert the dropdown just before the add-button wrapper
            $btn.closest('.form-control').before(new_field);
        }
    });

    function addEditor(new_field, editor_id, field_key) {

        // Create a new textarea and append it to the container
        var newTextarea = $('<textarea>', {
            id: editor_id,
            class: 'req-desc wp-editor-area',
            name: field_key + '[req_desc]',
            rows: '5'
        });

        new_field.find('.req-input-label-desc').after(newTextarea);

        new_field.find('.file-required-label').addClass('hidden-label');
        new_field.find('.textarea-required-label').removeClass('hidden-label');

        // Initialize TinyMCE editor
        wp.editor.initialize(editor_id, {
            tinymce: {
                toolbar1: 'bold,italic,underline,|,bullist,numlist,|,link,|,undo,redo',
                toolbar2: '',
                content_style: 'body, p, div { font-family: "Open Sans", sans-serif; color: #4c5866; }' // Enclose Open Sans in quotes
            },
            quicktags: {
                buttons: 'strong,em,link,ul,ol,li,quote',
            }
        });
    }

    $(document).on('click', '.service-requirements-field-remove', function () {
        $(this).closest('.service-requirements-field').remove();
    });
    $(document).on('click', '.service-milestones-field-remove', function () {
        $(this).closest('.service-milestones-field').remove();
    });

    // hide the close button for first requirement.
    if ($('.service-requirements-fields').length) {
        jQuery('.service-requirements-fields .service-requirements-field:first .service-requirements-field-remove').addClass('hidden');
    }

    // Make Requirements Fields Sortable.
    if ($('#service-requirements-fields').length) {
        $('#service-requirements-fields').sortable({
            handle: ".field-drag-handle",
            containment: "#service-requirements-fields",
            cancel: ".no-sort",
            cursor: 'row-resize',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            update: function (event, ui) {

                var service_setting_id = $('#surelywp-sv-service-setting-id').val();

                $('.service-requirements-fields .service-requirements-field').each(function (index, element) {

                    $('.req-field-type', element).attr('name', 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][req_fields][' + index + '][req_field_type]');
                    $('.is-require-field', element).attr('name', 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][req_fields][' + index + '][is_required_field]');
                    $('.req-title', element).attr('name', 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][req_fields][' + index + '][req_title]');
                    $('.req-desc', element).attr('name', 'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][req_fields][' + index + '][req_desc]');

                    if (index === 0) {
                        // Add 'hidden' class to the remove button of the first field
                        $('.service-requirements-field-remove', element).addClass('hidden');
                    } else {
                        // Remove 'hidden' class from the remove button of all other fields
                        $('.service-requirements-field-remove', element).removeClass('hidden');
                    }
                });
            }
        });
    }

    if ($('#service-milestones-fields').length) {
        $('#service-milestones-fields').sortable({
            handle: ".field-drag-handle",
            containment: "#service-milestones-fields",
            cancel: ".no-sort",
            cursor: 'row-resize',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            update: function (event, ui) {

                var service_setting_id = $('#surelywp-sv-service-setting-id').val();

                $('.service-milestones-fields .service-milestones-field').each(function (index, element) {

                    $('.milestones-field-label', element).attr(
                        'name',
                        'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + index + '][milestones_field_label]'
                    );
                    $('input[name*="[milestones_require_approval]"]', element).attr(
                        'name',
                        'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + index + '][milestones_require_approval]'
                    );
                    $('input[name*="[milestones_revision_allowed]"]', element).attr(
                        'name',
                        'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + index + '][milestones_revision_allowed]'
                    );
                    $('input[name*="[milestone_revisions_number]"]', element).attr(
                        'name',
                        'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + index + '][milestone_revisions_number]'
                    );
                    $('input[name*="[milestone_delivery_days]"]', element).attr(
                        'name',
                        'surelywp_sv_settings_options[surelywp_sv_settings_options][' + service_setting_id + '][milestones_fields][' + index + '][milestone_delivery_days]'
                    );

                    if (index === 0) {
                        // First milestone remove button hidden
                        $('.service-milestones-field-remove', element).addClass('hidden');
                    } else {
                        // Other milestones remove button visible
                        $('.service-milestones-field-remove', element).removeClass('hidden');
                    }
                });
            }
        });
    }


    jQuery(document).ready(function($) {
        $('#dropdown-fields-wrapper').sortable({
            handle: ".dropdown-field-drag-handle",
            // placeholder: 'sortable-placeholder',
            // axis: 'y',
            // containment: "#req-input-dropdown",
            cancel: ".no-sort",
            cursor: 'row-resize',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            // update: function (event, ui) {
            // }
        });
    });

    // show extent delivery date modal
    $(document).on('click', '#extend-delivery-button', function (e) {

        e.preventDefault();
        $('.exdent-delivery-date').addClass('show-modal');
    });

    // on close delivery model
    $(document).on('click', '.close-button', function (e) {
        $(this).closest('.surelywp-sv-modal').find('.modal').removeClass('show-modal');
        reset_delivery_date();
    });

    // cancel change delivery date
    $(document).on('click', '#cancel-delivery-date-change', function (e) {
        e.preventDefault();
        $(this).closest('.surelywp-sv-modal').find('.modal').removeClass('show-modal');
        reset_delivery_date();
    });

    function reset_delivery_date() {

        var delivery_date = new Date($('.delivery-time').text());

        // Format the date to YYYY-MM-DD
        var formattedDate = delivery_date.getFullYear() + '-' +
            ('0' + (delivery_date.getMonth() + 1)).slice(-2) + '-' +
            ('0' + delivery_date.getDate()).slice(-2);

        // Set the formatted date as the value of the date input
        $('#change-delivery-time').val(formattedDate);
    }

    // confirm change delivery date
    $(document).on('click', '#confirm-change-delivery-date', function (e) {

        e.preventDefault();
        $('.body').addClass('pointer-event-none');
        $('.exdent-delivery-date .modal-top .loader').removeClass('hidden');

        var delivery_date = $('#change-delivery-time').val();
        var service_id = $('#surelywp-sv-service-id').val();
        var nonce = $('#surelywp_sv_delivery_change_nonce').val();

        $.ajax({
            url: sv_backend_ajax_object.ajax_url,
            dataType: "json",
            type: "POST",
            data: {
                action: "surelywp_sv_change_delivery_date",
                nonce: nonce,
                service_id: service_id,
                delivery_date: delivery_date,
            },
            success: function (res) {
                window.location.reload();
                $('.exdent-delivery-date .modal-top .loader').addClass('hidden');
                $('.delivery-date-change, body').removeClass('pointer-event-none');
            },
        }).catch((error) => {
            $('.exdent-delivery-date .modal-top .loader').addClass('hidden');
            $('body').removeClass('pointer-event-none');
            console.error("Error:", error);
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

    // show delivery now modal
    $(document).on('click', '#delivery-now-button', function (e) {

        e.preventDefault();
        $('.delivery-now').addClass('show-modal');
    });

    // close delivery now modal
    $(document).on('click', '#cancel-delivery-now', function (e) {
        $(this).closest('.surelywp-sv-modal').find('.modal').removeClass('show-modal');
        $('.service-message-input').val('');
        delivery_now_filepond.removeFiles();
    });

    // Handle delivery now form
    $(document).on('click', '#confirm-delivery-now', function (e) {

        // remove previous errors.
        if ($('.sv-error').length > 0) {
            $('.sv-error').remove();
        }

        $(".delivery-now.modal .modal-content").animate({ scrollTop: 0 }, "slow");

        // Message Form handler.
        var formData = new FormData();
        var nonce = $("#surelywp_sv_message_form_submit_nonce").val();
        var service_id = $("#surelywp-sv-service-id").val();
        var receiver_id = $("#message-receiver-id").val();
        var is_milestone_delivery = $('#is-final-delivery').attr('data-milestone');
        var service_setting_id = $("#surelywp-sv-service-setting-id").val();

        var service_message = tinyMCE.get('service-message-input-delivery-now') ? tinyMCE.get('service-message-input-delivery-now').getContent() : $('#service-message-input-delivery-now').val();
        if ('' === service_message) {
            service_message = $('#service-message-input-delivery-now').val();
        }

        var is_final_delivery = 1;

        var is_milestone_delivery_flag = 0;
        if (parseInt(is_milestone_delivery) !== 0) {
            is_milestone_delivery_flag = 1;
        } else {
            is_milestone_delivery_flag = 0;
        }

        // Get allow message files
        var msg_attachment_files = delivery_now_filepond.getFiles();
        $.each(msg_attachment_files, function (index, fileItem) {

            if (fileItem.status == 2) { // Status 2 indicates an success
                formData.append('msg_attachment_file[]', fileItem.file);
            } else {
                delivery_now_filepond.removeFile(fileItem.id);
            }
        });

        if ('' === service_message || undefined === service_message) {
            var error_msg = wp.i18n.__('Please enter your message and try again.', 'surelywp-services');
            $('.delivery-now-wrap').after('<p class="sv-error">' + error_msg + '</p>');
            return;
        }

        $('.body').addClass('pointer-event-none');
        $('.delivery-now .modal-top .loader').removeClass('hidden');

        formData.append('action', 'surelywp_sv_message_form_sumbit_callback');
        formData.append('service_id', service_id);
        formData.append('receiver_id', receiver_id);
        formData.append('is_final_delivery', is_final_delivery);
        formData.append('is_milestone_delivery', is_milestone_delivery);
        formData.append('is_milestone_delivery_flag', is_milestone_delivery_flag);
        formData.append('nonce', nonce);
        formData.append('service_message', service_message);
        formData.append('service_setting_id', service_setting_id);
        

        $.ajax({
            url: sv_backend_ajax_object.ajax_url,
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {

                $('.body').removeClass('pointer-event-none');
                $('.delivery-now .modal-top .loader').addClass('hidden');
                if (!res.success) {
                    $('.delivery-now-wrap').after('<p class="sv-error">' + res.message + '</p>');
                } else {
                    var success_message = 'Successfully Delivered.';
                    $('.delivery-now-wrap').after('<p class="sv-success">' + success_message + '</p>');
                }
                window.location.reload();

                if (tinyMCE.get('service-message-input-delivery-now')) {
                    tinyMCE.get('service-message-input-delivery-now').setContent('')
                }

                $('#service-message-input-delivery-now').val('');

                setTimeout(function () {
                    delivery_now_filepond.removeFiles();
                }, 100
                );
            },
        });
    });

    // handle actions of header.

    $(document).on('click', '#service-action-btn', function (e) {

        var service_status = $('#surelywp-sv-service-status').val();
        var before_service_start_status = ['waiting_for_req', 'waiting_for_contract'];

        // For 'Service Start' Action.
        if (before_service_start_status.includes(service_status)) {
            $('.service-start-action').removeClass('hidden');
        } else {
            $('.service-start-action').addClass('hidden');
        }

        // For 'Service Complete' Action.
        if ('service_complete' === service_status) {
            $('.service-complete-action').addClass('hidden');
        } else {
            $('.service-complete-action').removeClass('hidden');
        }

        // For 'Service Cancled' Action.
        if ('service_canceled' === service_status) {
            $('.service-canceled-action').addClass('hidden');
            $('.service-delete-action').removeClass('hidden');
        } else {
            $('.service-canceled-action').removeClass('hidden');
        }
    });

    $(document).on('click', '.sv-action-menu-item', function (e) {

        var service_status = $(this).val();

        if ('service_delete' === service_status) {
            return;
        }

        $('.confirm-service-update').addClass('show-modal');

        if ('service_complete' == service_status) {
            $('.confirm-service-complete').removeClass('hidden');
            $('.confirm-service-cancel').addClass('hidden');
            $('.confirm-service-start').addClass('hidden');

        } else if ('service_canceled' == service_status) {
            $('.confirm-service-cancel').removeClass('hidden');
            $('.confirm-service-complete').addClass('hidden');
            $('.confirm-service-start').addClass('hidden');
        } else if ('service_start' == service_status) {
            $('.confirm-service-start').removeClass('hidden');
            $('.confirm-service-cancel').addClass('hidden');
            $('.confirm-service-complete').addClass('hidden');
        }
    });

    // show loader.
    $(document).on('click', '.confirm-service-complete, .confirm-service-cancel, .confirm-service-start', function (e) {
        $('.confirm-service-update .modal-top .loader').removeClass('hidden');
    });
    // close confirm pop up.
    $(document).on('click', '#cancel-service-update', function (e) {
        $(this).closest('.surelywp-sv-modal').find('.modal').removeClass('show-modal');
    });

    $(document).on('scHide', function (e) {
        e.preventDefault();
        if ('work-start-note' === e.target.classList[0]) {
            var domain_name = window.location.hostname;
            var service_id = $('#surelywp-sv-service-id').val();
            setCookie('is_close_work_start_note_' + service_id, true, 365, "/", domain_name, true); // Sets a cookie with all options
        }
    });

    // hide the work start note if user have closed.
    if ($('.surelywp-sv-serview-view.admin').length) {

        var service_id = $('#surelywp-sv-service-id').val();
        if (service_id) {
            var is_close_alert_close = getCookie('is_close_work_start_note_' + service_id);
            if (is_close_alert_close) {
                $('.surelywp-sv-serview-view.admin .note').hide();
            }
        }
    }

    if ($('.surelywp-sv-serview-view.admin').length) {
        $('body').addClass('surelywp-service-admin-view');
    }

    // Delete Associaltive services modal.
    $(document).on('click', '#sv-remove-associate-service', function (e) {
        e.preventDefault();
        var delete_url = $(this).attr('href');
        $('.associative-service-delete').addClass('show-modal');
        $('#confirm-as-service-delete').attr('href', delete_url);
    });

    $(document).on('click', '.close-modal-button', function (e) {
        $(this).closest('.modal').removeClass('show-modal');
    });

    $(document).on('click', '.surelywp-sv-delete-service', function (e) {

        e.preventDefault();
        var delete_url = $(this).attr('href');
        $('#confirm-service-delete').attr('href', delete_url);
        $('.confirm-service-delete.modal').addClass('show-modal');
    });

    $(document).on('click', '#confirm-service-delete', function (e) {
        $(this).closest('.modal').find('.loader').removeClass('hidden');
    });

    var service_setting_id = $('#surelywp-sv-service-setting-id').val();
    var sv_active_tab_key = 'sv_active_tab_id_' + service_setting_id;
    var sv_active_tab_id = localStorage.getItem(sv_active_tab_key);

    if (!sv_active_tab_id) {
        sv_active_tab_id = 'sv-products-tab'; // default tab
    }

    // set the active tab.
    setTimeout(function () {
        $('#' + sv_active_tab_id).trigger('click');
    }, 100);

    // Manage the tab swtiching.
    $(document).on('click', '.services-settings-tabs .surelywp-btn', function (e) {

        $(this).addClass('active').closest('.services-settings-tabs').find('.surelywp-btn').not(this).removeClass('active');

        let tab_id = $(this).attr('id');

        // Save the active tab id.
        var current_service_id = $('#surelywp-sv-service-setting-id').val();
        localStorage.setItem('sv_active_tab_id_' + current_service_id, tab_id);

        $('.tab-settings').addClass('hidden');
        switch (tab_id) {
            case 'sv-general-tab':
                $('.sv-general-settings').removeClass('hidden');
                break;
            case 'sv-products-tab':
                $('.sv-products-settings').removeClass('hidden');
                break;
            case 'sv-rules-tab':
                $('.sv-rules-settings').removeClass('hidden');
                break;
            case 'sv-contract-tab':
                $('.sv-contract-settings').removeClass('hidden');
                break;
            case 'sv-requirements-tab':
                $('.sv-requirements-settings').removeClass('hidden');
                break;
            case 'sv-milestones-tab':
                $('.sv-milestones-settings').removeClass('hidden');
                break;
        }
    });

    // Import Export Settings
    FilePond.registerPlugin(

        // validates the size of the file
        FilePondPluginFileValidateSize,

        // validates the type of the file
        FilePondPluginFileValidateType
    );

    // Turn input into FilePond instance (single file only)
    const surelywp_sv_import_settings = FilePond.create(document.querySelector('.messages-filepond-sv-import-export'), {
        acceptedFileTypes: sv_backend_ajax_object.file_types_ie,
        allowFileEncode: false,
        allowMultiple: true,
        labelIdle: wp.i18n.__('Click to upload', 'surelywp-catalog-mode') + ' <span class="filepond--label-action">' + wp.i18n.__('or', 'surelywp-catalog-mode') + '</span>' + wp.i18n.__(' drag and drop', 'surelywp-catalog-mode') + ' <span class="filepond--label-action">' + wp.i18n.__(' Only JSON File is allowed', 'surelywp-services') + '</span>'
    });

    $('#import-settings-form').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this); // grab nonce + hidden fields

        // Append single FilePond file (if exists)
        if (surelywp_sv_import_settings.getFiles().length > 0) {
            let fileItem = surelywp_sv_import_settings.getFiles()[0];
            if (fileItem.status === 2) { // success
                formData.append('import_sv_file', fileItem.file);
            }
        }

        formData.append('action', 'surelywp_sv_import_settings');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success == false) {
                    $('#surelywp_services_panel_surelywp_import_export').prepend('<div class="notice notice-error is-dismissible surelywp_sv_ie_settings_notice"><p>' + response.data.message + '</p></div>');
                    surelywp_sv_import_settings.removeFiles();
                } else {
                    $('#surelywp_services_panel_surelywp_import_export').prepend('<div class="notice notice-success is-dismissible surelywp_sv_ie_settings_notice"><p>' + wp.i18n.__('All Settings Of Services Plugin Imported Successfully.', 'surelywp-services') + '</p></div>');
                    surelywp_sv_import_settings.removeFiles();
                }
                setTimeout(function() {
                    $('.surelywp_sv_ie_settings_notice').fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 5000);
            },
            error: function(xhr) {
                $('#surelywp_services_panel_surelywp_import_export').prepend('<div class="notice notice-error is-dismissible"><p>' + xhr.responseText + '</p></div>');
                surelywp_sv_import_settings.removeFiles();
            }
        });
    });
});
