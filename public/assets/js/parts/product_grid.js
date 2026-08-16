$(document).on('click', '.color_image', function() {
    //e.stopImmediatePropagation();
    $show_img = $(this).attr('src');
    $color_selectd = $(this).data('title');
    $('.selectedColor').text($color_selectd);
    $(".selectedColor").attr('data-selectedColor', $color_selectd);
    $('#big-img').attr("src", $show_img);
    $('#show-img').attr("src", $show_img);
    $('.zoom-show').attr("src", $show_img);
    $('#big-img').css({ 'background': '#fff' });
    $('[id=\'big-img\']').css({ 'background': '#fff' });
    $('[id=\'big-img\']').attr("src", $show_img);
    $(this).closest('.option_parent_group').find('.color_image').css({ 'border': '0' });
    $(this).css({ 'border': '3px solid #2ca8e3' });
});


$(document).on('click', '.option_selector', function() {
    let additionalPrice = Number($(this).data('price'));
    let additionalQty = Number($(this).data('variable-qty'));
    let selectedTitle = $(this).data('title');
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variant_input').val(selectedTitle);
    parentElement.find('.variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.selectedValue').html(selectedTitle);
    parentElement.find('.option_selector').removeClass('option_selected');
    $(this).addClass('option_selected');
    changePrice();
});


$(document).on('change', '.variant_dropdwon', function() {
    let additionalPrice = Number($(this).find('option:selected').data('dropdownprice'));
    let additionalQty = Number($(this).find('option:selected').data('variable-qty'));
    let selectedTitle = $(this).find('option:selected').val();
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variant_input').val(selectedTitle);
    parentElement.find('.variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.selectedValue').html(selectedTitle);
    changePrice();
});

function changePrice() {
    let originalPrice = Number($('.calculated_price').data('calculated-price'));
    let additionalPrice = 0;
    $('.variant_input').each(function(key, val) {
        additionalPrice += Number($(this).attr('data-additional-price'));
    });
    let calculatedPrice = originalPrice + additionalPrice;
    let isSubmitAble = true;
    if(calculatedPrice < 0){
        isSubmitAble = false;
    }
    $('.price_text').text(calculatedPrice);
    $('.qty').attr('data-qty', 1);
    $('.qty').text(1);
    $('.qtyInput').val(1);
    $('.variant_input').each(function() {
        let currentQty = $(this).attr('data-additional-qty');
        if (currentQty == -1) {
            isSubmitAble = false;
        }
    });
    console.log(isSubmitAble);
    if (isSubmitAble) {
        $('.add_buy_variable button').removeClass('disabled');
        $('.add_buy_variable button').removeAttr('disabled');
        $('.add_buy_variable button').attr('type', 'submit');
    }else{
        $('.add_buy_variable button').addClass('disabled');
        $('.add_buy_variable button').attr('disabled',true);
        $('.add_buy_variable button').attr('type', 'button');
    }
}

$(document).on('click', '.change_location', function() {
    $('.pickup_location').removeAttr('readonly');
    $('.pickup_location').css({ 'border': '1px solid #cfcfcf;', 'background': '#fbfbfb' });
    return false;
});
$(".pickup_location").keypress(function(event) {
    event.stopImmediatePropagation();
    if (event.key === "Enter") {
        $('.pickup_location').attr('readonly', 'true');
        $('.pickup_location').css({ 'border': '0', 'background': 'rgb(247 247 247)' });
    }
});
$(document).on('mouseleave', '.delivery', function() {
    $('.pickup_location').attr('readonly', 'true');
    $('.pickup_location').css({ 'border': '0', 'background': 'rgb(247 247 247)' });
    return false;
});




$(document).on('click', '.nextImage', function() {
    var nextImage = $(this).closest('.small-img').find(".show-small-img[alt='now']").next().attr('src');
    $("img[data-image-section='"+$(this).attr('data-section-id')+"']").attr('src', nextImage);

    $('[id=\'big-img\']').attr('src', nextImage);
    $(".show-small-img[alt='now']").next().css({ 'border': 'solid 1px #951b25', 'padding': '2px' }).siblings().css({ 'border': 'none', 'padding': '0' });
    $(".show-small-img[alt='now']").next().attr('alt', 'now').siblings().removeAttr('alt')
    if ($('#small-img-roll').children().length > 4) {
        if ($(".show-small-img[alt='now']").index() >= 3 && $(".show-small-img[alt='now']").index() < $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($(".show-small-img[alt='now']").index() - 2) * 76 + 'px')
        } else if ($(".show-small-img[alt='now']").index() == $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px')
        } else {
            $('#small-img-roll').css('left', '0')
        }
    }
});


$(document).on('click', '.previewImage', function() {
    var previewImage = $(this).closest('.small-img').find(".show-small-img[alt='now']").prev().attr('src');
    $("img[data-image-section='"+$(this).attr('data-section-id')+"']").attr('src', previewImage);

    $(".show-small-img[alt='now']").prev().css({ 'border': 'solid 1px #951b25', 'padding': '2px' }).siblings().css({ 'border': 'none', 'padding': '0' })
    $(".show-small-img[alt='now']").prev().attr('alt', 'now').siblings().removeAttr('alt')
    if ($('#small-img-roll').children().length > 4) {
        if ($(".show-small-img[alt='now']").index() >= 3 && $(".show-small-img[alt='now']").index() < $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($(".show-small-img[alt='now']").index() - 2) * 76 + 'px')
        } else if ($(".show-small-img[alt='now']").index() == $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px')
        } else {
            $('#small-img-roll').css('left', '0')
        }
    }
});
$(document).on('click', '.show-small-img', function() {
    var clickImage = $(this).attr('src');
    $("img[data-image-section='"+$(this).attr('data-section-id')+"']").attr('src', clickImage);
    $('.show-small-img').css({ 'border': '0', 'padding':'0px'});
    $(this).css({ 'border': '1px solid rgb(149, 27, 37)', 'padding':'2px'});
    $('.show-small-img:first-of-type').css({ 'border': 'solid 1px #951b25', 'padding': '2px' });
    $('.show-small-img:first-of-type').attr('alt', 'now').siblings().removeAttr('alt');
    $('#show-img').attr('src', $(this).attr('src'));
    $('#big-img').attr('src', $(this).attr('src'));
    $(this).attr('alt', 'now').siblings().removeAttr('alt');
    $(this).css({ 'border': 'solid 1px #951b25', 'padding': '2px' }).siblings().css({ 'border': 'none', 'padding': '0' });
    if ($('#small-img-roll').children().length > 4) {
        if ($(this).index() >= 3 && $(this).index() < $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($(this).index() - 2) * 76 + 'px');
        } else if ($(this).index() == $('#small-img-roll').children().length - 1) {
            $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px');
        } else {
            $('#small-img-roll').css('left', '0');
        }
    }
});