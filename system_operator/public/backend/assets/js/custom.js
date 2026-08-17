

jQuery(document).on("click", ".clone_btn", function() {
    var item = jQuery(this)
        .closest(".clone_section")
        .find('input[type="file"]:first');
    item.clone().insertAfter(item);
});

jQuery(document).on("click", ".product_image_clone", function() {
    var counter = parseInt(jQuery(this).attr("data-counter"));
    var html =
        '<div class="col-sm-8">' +
        '<span class="clone_label"></span>' +
        '<input type="text" class="product-midia-toggle" id="my-file' +
        counter +
        '" name="image[]">' +
        '<button class="product-midia-toggle select_file" data-input="my-file' +
        counter +
        '">Select File</button>' +
        "</div>";
    jQuery(this).attr("data-counter", counter + 1);
    jQuery(".clone_content").append(html);
});

jQuery(document).on("keyup", "#reason", function() {
    var reason = jQuery("#reason").val();
    // alert(reason);
    url = jQuery(".delete_trigger").attr("data-href");
    url = url + "?reason=" + reason;
    jQuery(".delete_trigger").attr("href", url);
});

jQuery(document).on("click", ".delete_btn", function(e) {
    var url = jQuery(this).attr("data-url");
    jQuery("#deleteModal .delete_trigger").attr("href", url);
    jQuery("#deleteModal .delete_trigger").attr("data-href", url);

    jQuery("#deleteModal .delete_trigger_id").attr("href", url);
});

jQuery(document).on("click", ".delete_trigger", function(e) {
    if (jQuery("#reason").val() == "") {
        e.preventDefault();
        alert(
            "You need to specify the reasson why you want to delete this item."
        );
        return;
    }
});

jQuery(document).on("click", ".delete_btn_resource", function() {
    var url = jQuery(this).attr("data-url");
    jQuery("#deleteModal .delete_trigger").attr("href", url);
    jQuery("#deleteModal .delete_trigger").attr("data-href", url);
    jQuery(".delete_form").attr("action", url);
});

tinymce.init({
    selector: ".textEditor",
    height: 300,
    theme: "modern",
    plugins: "print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern code help",
    toolbar1: "fontselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code toc",
    image_advtab: true,
    font_formats: "Arial Black=arial black,avant garde;Indie Flower=indie flower, cursive;Times New Roman=times new roman,times;Roboto=roboto",
});

jQuery(document).ready(function() {
    jQuery("#listTable").DataTable({
        dom: "Brftlip",
        buttons: ["csv", "excel", "pdf", "print"],
        order: [],
        columnDefs: [{
            targets: [0],
            //'targets': 'no-sort',
            orderable: false,
        }, ],
    });
});

jQuery(document).ready(function() {
    jQuery("#listTable2").DataTable({
        dom: "Brftlip",
        buttons: [],
        order: [],
        columnDefs: [{
            targets: [0],
            //'targets': 'no-sort',
            orderable: false,
        }, ],
    });
});

jQuery(document).ready(function() {
    setTimeout(function() {
        jQuery(".alert").hide(300);
    }, 10000);
});

jQuery(function() {
    jQuery(".sortable,#option_div").sortable();
    jQuery(".sortable,#option_div").disableSelection();
});

jQuery(function() {
    jQuery("#sortable").sortable();
    jQuery("#sortable").disableSelection();
});

jQuery(document).on("click", ".add_values", function() {
    var number = parseInt(jQuery(this).attr("data-added-value"));
    var OptionNumber =
        parseInt(jQuery("#add_options").attr("data-added-option")) - 1;
    var valueSection = jQuery("#Options .option_div:last-child .sortable");
    var html =
        '<li class="ui-state-default">' +
        '<div class="row">' +
        '<div class="col-md-4">' +
        '<div class="form-group">' +
        '<input type="text"  name="option[' +
        OptionNumber +
        "][value][" +
        number +
        '][title]" placeholder="Title" class="form-control value_title" />' +
        "</div>" +
        "</div>" +
        '<div class="col-md-2">' +
        '<div class="form-group">' +
        '<input type="text" name="option[' +
        OptionNumber +
        "][value][" +
        number +
        '][sku]" placeholder="SKU" value="' +
        randomAlphaNumeric() +
        '" class="form-control value_sku" />' +
        "</div>" +
        "</div>" +
        '<div class="col-md-1">' +
        '<div class="form-group">' +
        '<input type="number" name="option[' +
        OptionNumber +
        "][value][" +
        number +
        '][qty]" placeholder="Qty" min="0" class="form-control value_qty" />' +
        "</div>" +
        "</div>" +
        '<div class="col-md-2 variant_values">' +
        '<div class="form-group">' +
        '<button type="button" data-image-width="1000" data-image-height="1000" data-input-name="option[' +
        OptionNumber +
        "][value][" +
        number +
        '][variant_image]" data-input-type="single" class="btn btn-success initConcaveMedia" >Image</button>' +
        "</div>" +
        "</div>" +
        '<div class="col-md-2">' +
        '<div class="form-group">' +
        '<input type="number" name="option[' +
        OptionNumber +
        "][value][" +
        number +
        '][price]" required value="0" placeholder="Aditional Price" class="form-control value_price" />' +
        "</div>" +
        "</div>" +
        '<div class="col-md-1">' +
        '<i class="mdi mdi-delete text-danger delete_btn"></i>' +
        "</div>" +
        "</div>" +
        "</li>";
    valueSection.append(html);
    jQuery(this).attr("data-added-value", number + 1);
});

jQuery(document).on("click", "#add_options", function() {
    var optionSection = jQuery("#option_div");
    var number = parseInt(jQuery(this).attr("data-added-option"));

    var html =
        '<div class="card mb-2 option_div">' +
        '<i class="mdi mdi-close-circle text-danger close_option"></i>' +
        '<span class="mdi mdi-drag option_drag"></span>' +
        '<div class="card-body">' +
        '<div class="row">' +
        '<div class="col-md-4">' +
        '<div class="form-group row">' +
        '<label class="col-sm-3 col-form-label">Title</label>' +
        '<div class="col-sm-9">' +
        '<input type="text" name="option[' +
        number +
        '][title]" class="form-control option_title" />' +
        "</div>" +
        "</div>" +
        "</div>" +
        '<div class="col-md-4">' +
        '<div class="form-group row">' +
        '<label class="col-sm-3 col-form-label">Type</label>' +
        '<div class="col-sm-9">' +
        '<select class="form-control option_type" name="option[' +
        number +
        '][type]" >' +
        // '<option value="text">Text</option>' +
        '<option value="dropdown">Dropdown</option>' +
        '<option value="radio">Radio Button</option>' +
        "</select>" +
        "</div>" +
        "</div>" +
        "</div>" +
        '<div class="col-md-4">' +
        '<div class="form-group row">' +
        '<label class="col-sm-3 col-form-label"></label>' +
        '<div class="col-sm-9">' +
        '<input type="hidden" name="option[' +
        number +
        '][is_required]" value="1">' +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>" +
        '<ul class="sortable"></ul>' +
        '<p><a data-added-value="0" class="btn btn-info float-right add_values mt-2" href="javascript:void(0)">Add Value</a></p>' +
        "</div>" +
        "</div>";

    optionSection.append(html);
    jQuery(this).attr("data-added-option", number + 1);
});

jQuery(document).on("click", "#add_tab_opiton", function() {
    var optionSection = jQuery("#add_here_tab_opiton");
    var number = parseInt(jQuery(this).attr("data-added-option"));
    var html =
        '<div class="card mb-2 option_div">' +
        '<i class="mdi mdi-close-circle text-danger close_option"></i>' +
        '<div class="card-body">' +
        '<div class="row">' +
        '<div class="col-md-12">' +
        '<div class="form-group row">' +
        "<label>Title</label>" +
        '<input type="text" name="tab_option[' +
        number +
        '][tab_option_title]" class="form-control tab_option_title" />' +
        "</div>" +
        "</div>" +
        '<div class="col-md-12">' +
        '<div class="form-group row">' +
        "<label>Description</label>" +
        '<textarea type="text" col="10" row="10" name="tab_option[' +
        number +
        '][tab_option_description]" class="form-control tab_option_description" id="tab_option_description" ></textarea>' +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>";

    optionSection.append(html);
    jQuery(this).attr("data-added-option", number + 1);
});

// jQuery(document).on('click', '.delete_btn', function() {
//     jQuery(this).closest('li').remove();
// });
jQuery(document).on("click", ".close_option", function() {
    jQuery(this).closest(".option_div").remove();
});

function randomAlphaNumeric() {
    var characters = "abcdefghijklmnopqrstuvwxyz0123456789";
    var result = "";
    var charactersLength = characters.length;

    for (var i = 0; i < 5; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength)
        );
    }

    return result;
}

jQuery(document).on("click", "#add_global_option", function() {
    var option_id = jQuery(this)
        .closest(".button_section")
        .find("select option:selected")
        .val();
    var that = jQuery(this);
    var section = jQuery("#Options .option_div:last-child");
    var OptionNumber =
        parseInt(jQuery("#add_options").attr("data-added-option")) - 1;
    if (section.length == 0) {
        alert("Please add option first!");
        return;
    }

    jQuery.ajax({
        url: jQuery(that).attr("data-action-url"),
        cache: false,
        type: "POST",
        data: {
            _token: jQuery(that).attr("data-csrf-token"),
            id: option_id,
        },
        success: function(response) {
            var data = JSON.parse(response);
            section.find(".option_title").val(data.option.title);
            section
                .find('.option_type option[value="' + data.option.type + '"]')
                .attr("selected", "selected");
            if (data.option.is_required == 1) {
                section.find(".option_is_required").attr("checked", "checked");
            }

            var valueSection = jQuery(
                "#Options .option_div:last-child .sortable"
            );
            var valueCount = 0;
            jQuery.map(data.values, function(value, index) {
                valueSection.append(
                    '<li class="ui-state-default"><div class="row">' +
                    '<span class="mdi mdi-drag"></span>' +
                    '<div class="col-md-3">' +
                    '<div class="form-group">' +
                    '<input type="text"  name="option[' +
                    OptionNumber +
                    "][value][" +
                    index +
                    '][title]" value="' +
                    value.title +
                    '" placeholder="Title" class="form-control value_title" />' +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-3">' +
                    '<div class="form-group">' +
                    '<input type="text" name="option[' +
                    OptionNumber +
                    "][value][" +
                    index +
                    '][sku]" value="' +
                    value.sku +
                    '" placeholder="SKU" value="' +
                    randomAlphaNumeric() +
                    '" class="form-control value_sku" />' +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-3">' +
                    '<div class="form-group">' +
                    '<input type="text" name="option[' +
                    OptionNumber +
                    "][value][" +
                    index +
                    '][price]" value="' +
                    value.price +
                    '" placeholder="price" class="form-control value_price" />' +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<select class="form-control value_price_type" name="option[' +
                    OptionNumber +
                    "][value][" +
                    index +
                    '][price_type]"  >' +
                    '<option value="' +
                    value.price_type +
                    '">' +
                    value.price_type +
                    "</option>" +
                    "</select>" +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-1">' +
                    '<i class="mdi mdi-delete text-danger delete_btn"></i>' +
                    "</div>" +
                    "</div>" +
                    "</li>"
                );
                valueCount++;
            });

            section.find(".add_values").attr("data-added-value", valueCount);
        },
    });
});

//Add Option Section
jQuery(document).on("click", "#add_values", function() {
    jQuery("#sortable li:last-child")
        .clone()
        .insertAfter("#sortable li:last-child");
    jQuery("#sortable li:last-child .form-control").each(function() {
        var number = jQuery(this).attr("name").split("[")[1].slice(0, -1);
        number = parseInt(number) + 1;
        var str = jQuery(this).attr("name");
        str = str.replace(number - 1, number);
        jQuery(this).attr("name", str);
    });
});

jQuery(document).on("change", "select[name=manage_stock]", function() {
    if (jQuery(this).children("option:selected").val() == 1) {
        jQuery("input[name=qty],select[name=in_stock]").attr("disabled", false);
    } else {
        jQuery("input[name=qty],select[name=in_stock]").attr("disabled", true);
    }
});

jQuery(document).ready(function() {
    jQuery("#sidebar ul li").removeClass("active");
});

jQuery(document).on("change", "#attribute_set", function() {
    var attributeSetId = jQuery(this).find("option:selected").val();
    jQuery("#dynamic_fields").html(
        '<p class="text-center mt-5 mb-5"><i class="text-primary mdi mdi-spin mdi-settings"></i> Please wait while attributes are loading..</p>'
    );

    if (attributeSetId != -1) {
        jQuery.ajax({
            url: "/admin/get-attribute-set-details/" + attributeSetId,
            type: "get",
            data: {},
            success: function(response) {
                jQuery("#dynamic_fields").html(response);
                tinymce.init({ selector: ".textEditor" });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            },
        });
    } else {
        jQuery("#dynamic_fields").html("");
    }
});

//Add custom values

jQuery(document).on("click", "#add_product_attribute_options", function() {
    let i = parseInt(jQuery(this).attr("data-count")) + 1;
    let html =
        '<div class="form-group row duplicate_attribute_box">' +
        '<label class="col-sm-2 col-form-label"></label>' +
        '<div class="col-sm-4">' +
        '<input type="text" placeholder="Option Label" class="form-control" name="attribute_values[' +
        i +
        '][label]">' +
        "</div>" +
        '<div class="col-sm-4">' +
        '<input type="text" placeholder="Option Value" class="form-control" name="attribute_values[' +
        i +
        '][value]">' +
        "</div>" +
        '<div class="col-sm-2"><button class="white-text btn btn-danger mdi mdi-delete text-danger rm_delete_btn">Remove</button></div>' +
        "</div>";
    jQuery(".dynamic_attribute_box").append(html);
    jQuery(this).attr("data-count", i);
});

jQuery(document).on("click", ".rm_delete_btn", function() {
    jQuery(this).closest(".duplicate_attribute_box").remove();
});

jQuery(document).on("change", ".option_type", function() {
    let optionTypeValue = jQuery(this).find("option:selected").val();
    jQuery(".custom_values_section").hide();
    if (
        optionTypeValue == "radio" ||
        optionTypeValue == "checkbox" ||
        optionTypeValue == "dropdown"
    ) {
        jQuery(".custom_values_section").show();
    }
});

jQuery(document).ready(function() {
    jQuery(".menu_active")
        .closest(".parent_group")
        .find("a.nav-link")
        .trigger("click");
});

jQuery(() => {
    jQuery(".datepicker").datepicker();
});

jQuery(document).on("click", "#submit_product_type", function() {
    let optionTypeValue = jQuery(this)
        .closest(".product_type_selector")
        .find("#product_type option:selected")
        .val();

    if (optionTypeValue == "simple") {
        jQuery('.nav-link[href="#Options"]').hide();
        jQuery(".create_product_title").html("Create New Simple Product");
    } else if (optionTypeValue == "variable") {
        jQuery(".create_product_title").html("Create New Variable Product");
    } else if (optionTypeValue == "digital") {
        jQuery(".create_product_title").html("Create New Digital Product");
    }
});

jQuery(document).on("change", ".slug_maker,.slug_taker", function() {
    var inputValue = jQuery(this).val();
    var inputName = jQuery(this).attr("name");
    var modelName = jQuery(this).attr("data-slugable-model");
    jQuery.ajax({
        url: "/admin/generate-slug/",
        type: "get",
        data: {
            inputName: inputName,
            inputValue: inputValue,
            modelName: modelName,
        },
        success: function(response) {
            jQuery(".slug_taker").val(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        },
    });
});

jQuery(document).on("submit", "#product_form", function(e) {
    var canSubmit = true;
    jQuery(".required").each(function(key, val) {
        var requiredInput = jQuery(this)
            .closest(".form-group")
            .find(".form-control");

        if (requiredInput.attr("name") != undefined) {
            var tabId = jQuery(this).closest(".tab-pane").attr("id");
            if (
                requiredInput.val() == "" &&
                requiredInput.attr("name") != "description"
            ) {


                requiredInput.css("background", "#ff00003b");
                jQuery('a[href="#' + tabId + '"]').css("color", "#f00");
                canSubmit = false;
            }
        }
    });

    if (!canSubmit) {
        e.preventDefault();
    }
});

jQuery(document).on("change", "#select_all", function() {
    if (jQuery("#select_all").attr("data-checked") == "true") {
        jQuery('input[type="checkbox"]').each(function(key, val) {
            jQuery(this).prop("checked", false);
            jQuery("#select_all").attr("data-checked", false);
        });
    } else {
        jQuery('input[type="checkbox"]').each(function(key, val) {
            jQuery(this).prop("checked", true);
            jQuery("#select_all").attr("data-checked", true);
        });
    }
});

jQuery(document).on("click", "#toggle_sidebar", function() {
    if (jQuery("body").hasClass("sidebar_small")) {
        jQuery("body").removeClass("sidebar_small");
        jQuery(".brand-logo img").attr(
            "src",
            "/uploads/images/backend-logo.png"
        );
    } else {
        jQuery("body").addClass("sidebar_small");
        jQuery(".brand-logo img").attr(
            "src",
            "/backend/assets/images/logo-small.png"
        );
    }
});

jQuery(document).on("click", ".edit_category_btn", function() {
    jQuery(".category_form_element").hide();
    jQuery(".category_form_element").after(
        '<h4 class="mt-5 mb-5 cat_info text-center"><i class="text-danger mdi mdi-spin mdi-loading"></i>  Loading Category Information...</h4>'
    );
    jQuery.ajax({
        url: "/admin/categories/edit/" + jQuery(this).attr("data-category-id"),
        type: "get",
        data: {},
        success: function(response) {
            jQuery(".category_form_element").html(response);
            jQuery(".category_form_element").show();
            jQuery(".cat_info").remove();
            tinymce.init({ selector: ".textEditor" });
            // $('.selectpicker').selectpicker();
            jQuery(".selectpicker").selectpicker();
            jQuery("#listTable3").DataTable({
                dom: "Brftlip",
                buttons: [],
                order: [
                    [0, "asc"]
                ],
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        },
    });
});

jQuery(document).on("click", ".create_new_category", function() {
    jQuery.ajax({
        url: "/admin/categories/create/",
        type: "get",
        data: {},
        success: function(response) {
            jQuery(".category_form_element").html(response);
            tinymce.init({ selector: ".textEditor" });
            jQuery(".selectpicker").selectpicker();

            jQuery("#listTable2").DataTable({
                dom: "Brftlip",
                buttons: [],
                order: [
                    [0, "asc"]
                ],
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        },
    });
});

jQuery(document).ready(function() {
    jQuery(".initConcaveMedia").each(function(key, val) {
        var widthText = jQuery(this).attr("data-image-width");
        var heightText = jQuery(this).attr("data-image-height");
        jQuery(this).after(
            '<small style="color:red;font-style:italic;margin-left: 10px;white-space: nowrap;">Image Size (width:' +
            widthText +
            " X height:" +
            heightText +
            ")</small>"
        );
    });
    jQuery("#product_sale_option").trigger("change");

    if (window.matchMedia("screen and (max-width: 991px)").matches) {
        jQuery("body").addClass("sidebar_small");
        jQuery(".brand-logo-mini img").attr(
            "src",
            "/backend/assets/images/favicon.png"
        );
    }
});

jQuery(document).on("change", "#product_sale_option", function() {
    jQuery(".downloadable_file_section").hide();
    if (jQuery(this).find("option:selected").val() == "downloadable") {
        jQuery(".downloadable_file_section").show();
    } else {
        jQuery(".downloadable_file_section").hide();
    }
});

$(document).ready(function() {
    var bg = $("#payment_status option:selected").val();
    $("#payment_status").css({ background: "" + bg, color: "#fff" });
});

$(document).on("change", "#payment_status", function() {
    jQuery("#reason,#payment_status_submit").show();
});

$(document).on("click", "#payment_status_submit", function() {
    var bg = $("#payment_status option:selected").val();
    var transaction_id = $("#payment_status option:selected").attr(
        "data-transactionid"
    );
    var status_id = $("#payment_status option:selected").attr("data-statusid");
    var reason = $("#reason").val();

    $.ajax({
        url: "/admin/change-payment-status",
        type: "post",
        data: {
            transaction_id: transaction_id,
            status_id: status_id,
            reason: reason,
        },
        success: function(response) {
            if (response == 1) {
                $("#payment_status").css({
                    background: "" + bg,
                    color: "#fff",
                });
                $(".status_message").text(
                    "Payment status changed successfully !."
                );
                setTimeout(function() {
                    $(".status_message").text("");
                    window.location.reload();
                }, 1000);
            } else {
                alert(response.message || "Something went wrong.");
            }
        },
    });
});

$(document).on("keyup", "#search_dashboard", function() {
    var keyword = $(this).val();

    if (keyword == "") {
        $("#search_dashboard_results").hide();
    } else {
        $("#search_dashboard_results").show();
    }

    $.ajax({
        url: "/admin/search-dashboard",
        type: "get",
        data: { keyword: keyword },
        cache: false,
        success: function(response) {
            $("#search_dashboard_results").html(response);
        },
    });
});

function toggleFullScreen(elem) {
    jQuery("#toggle_sidebar").trigger("click");
    if (
        (document.fullScreenElement !== undefined &&
            document.fullScreenElement === null) ||
        (document.msFullscreenElement !== undefined &&
            document.msFullscreenElement === null) ||
        (document.mozFullScreen !== undefined && !document.mozFullScreen) ||
        (document.webkitIsFullScreen !== undefined &&
            !document.webkitIsFullScreen)
    ) {
        if (elem.requestFullScreen) {
            elem.requestFullScreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullScreen) {
            elem.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}


jQuery(document).on('click', '.n_close', function() {
    jQuery(this).closest('.toast_notification').hide(600);
})


jQuery(document).ready(function() {
    setInterval(function() {
        var currentNotifications = jQuery('#sync_notification').attr('data-notification');

        jQuery(document).ready(function() {
            jQuery.ajax({
                type: 'GET',
                url: "/admin/get-live-notification?currentNotifications=" + currentNotifications,
                success: function(response) {
                    if (response.status == 1) {
                        jQuery('#live_notofication').html(response.data);
                        jQuery('#sync_notification').attr('data-notification', response.count_number);
                        jQuery('#sync_notification').text(response.count_number);

                        setTimeout(function() {
                            jQuery('.toast_notification').hide(600);
                        }, 4000);

                    }
                }
            });

        });


    }, 5000);

});
