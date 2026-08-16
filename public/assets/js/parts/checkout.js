

//Checkout
$(".step_1_btn").click(function(){
    if ($(".shipping_first_name").val() && $(".shipping_last_name").val() && $(".shipping_address").val() && $(".shipping_division").val() && $(".shipping_district").val() && $(".shipping_thana").val() && $(".shipping_postcode").val() && $(".shipping_phone").val() && $(".shipping_email").val()) {
        $('.circle-2').css({ 'background': '#0093d9' });
        $('.circle-2-text').css({ 'color': '#0093d9' });
        $('.circle-2').html('<i class="fa fa-check" aria-hidden="true"></i>');

        $('#step_container .steps_wrapper').css({ 'margin-left': '-1100px', 'transition': '1s' });
    } else {
        swal("Ops", 'Please fill up all information field.', "error");
    }

});


$(".step_2_btn").click(function(){
    if ($(".info_career:checked").val()) {
        $('.circle-2, .circle-3').css({ 'background': '#0093d9' });
        $('.circle-2-text, .circle-3-text').css({ 'color': '#0093d9' });
        $('.circle-2, .circle-3').html('<i class="fa fa-check" aria-hidden="true"></i>');
        $('#step_container .steps_wrapper').css({ 'margin-left': '-2200px', 'transition': '1s' });
        return false;
    } else {
        swal("Ops", 'Please select any shipping method.', "error");
        return false;
    }
});

$(".step_3_btn").click(function(){
    if ($(".info_career:checked").val() && $(".paymentcheck:checked").val()) {
        $('.circle-4').css({ 'background': '#0093d9' });
        $('.circle-4-text').css({ 'color': '#0093d9' });
        $('.circle-4').html('<i class="fa fa-check" aria-hidden="true"></i>');
        $('#step_container .steps_wrapper').css({ 'margin-left': '-3300px', 'transition': '1s' });
        return false;
    } else {
        swal("Ops", 'Please select shipping method or payment method.', "error");
        return false;
    }
});


$(".back_1_btn").click(function(){
    $('.circle-2').css({ 'background': '#456' });
    $('.circle-2-text').css({ 'color': '#456' });
    $('.circle-2').html('2');
    $('#step_container .steps_wrapper').css({ 'margin-left': '0px', 'transition': '1s' });
    return false;

});

$(".back_2_btn").click(function(){
    $('.circle-2, .circle-3').css({ 'background': '#456' });
    $('.circle-2-text, .circle-3-text').css({ 'color': '#456' });
    $('.circle-2').html('2');
    $('.circle-3').html('3');
    $('#step_container .steps_wrapper').css({ 'margin-left': '-1100px', 'transition': '1s' });
    return false;

});


$(".back_3_btn").click(function(){
    $('.circle-3').css({ 'background': '#456' });
    $('.circle-3-text').css({ 'color': '#456' });
    $('.circle-3').html('3');
    $('#step_container .steps_wrapper').css({ 'margin-left': '-1100px', 'transition': '1s' });
    return false;
});