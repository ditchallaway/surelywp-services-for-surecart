jQuery(document).ready(function ($) {
        var cartItems = localStorage.getItem("surecart-local-storage");
        var lineItems_Count = 0;
        
        if( cartItems !== null ){
            var parsed = JSON.parse(cartItems);
            var activeMode = null;
            if (parsed.live && Object.keys(parsed.live).length > 0) {
                var objectKeys = Object.keys(parsed.live);
                activeMode = "live";
            } else if (parsed.test && Object.keys(parsed.test).length > 0) {
                activeMode = "test";
                var objectKeys = Object.keys(parsed.test);
            }
            
            if (objectKeys.length > 0) {
                // Get First object from the active mode(live/test) becuase there can be multiple objects in local storage. 
                // All Lineitems is always in first object.
                var firstKey = objectKeys[0]; // e.g., "6", "12", "99" etc
                var cartObj = parsed[activeMode][firstKey];
               if( cartObj !== null ){
                    lineItems_Count = cartObj.line_items_count || 0;
                }
            }
        }

        if (lineItems_Count <= 0 || cartItems === null) {
            $.ajax({
                url: sv_common_ajax_object.ajax_url,
                type: "POST",
                dataType: "json",
                data: {
                    action: "surelywp_sv_delete_transient_empty_cart",
                    lineItemsCount: lineItems_Count,
                },
                success: function (response) {
                    if (response.success) {
                    } else {
                        console.error(response.data.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });
        }
    });