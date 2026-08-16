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


$(document).on('click', '.option_selector', function(){
    let additionalPrice = Number($(this).data('price'));
    let additionalQty = Number($(this).data('variable-qty'));
    let additionalSku = $(this).data('variable-sku');
    let selectedTitle = $(this).data('title');
    let parentElement = $(this).closest('.option_parent_group');
    parentElement.find('.variant_input').val(selectedTitle);
    parentElement.find('.variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.variant_input').attr('data-additional-sku', additionalSku);
    parentElement.find('.selectedValue').html(selectedTitle);
    parentElement.find('.option_selector').removeClass('option_selected');
    $(this).addClass('option_selected');
    changePrice();
});


$(document).on('change', '.variant_dropdwon', function() {

    $show_img = $(this).find('option:selected').attr('data-src');
    if($show_img != ''){
        $('#big-img').attr("src", $show_img);
        $('#show-img').attr("src", $show_img);
        $('.zoom-show').attr("src", $show_img);
        $('#big-img').css({ 'background': '#fff' });
        $('[id=\'big-img\']').css({ 'background': '#fff' });
        $('[id=\'big-img\']').attr("src", $show_img);
    }

    
    
    let additionalPrice = Number($(this).find('option:selected').data('dropdownprice'));
    let additionalQty = Number($(this).find('option:selected').data('variable-qty'));
    let additionalSku = $(this).find('option:selected').data('variable-sku');
    let selectedTitle = $(this).find('option:selected').val();
    let parentElement = $(this).closest('.option_parent_group');

    
    parentElement.find('.variant_input').val(selectedTitle);
    parentElement.find('.variant_input').attr('data-additional-price', additionalPrice);
    parentElement.find('.variant_input').attr('data-additional-qty', additionalQty);
    parentElement.find('.variant_input').attr('data-additional-sku', additionalSku);
    parentElement.find('.selectedValue').html(selectedTitle);
    changePrice();

});

function changePrice() {
    // let additionalPriceselected = Number($('.variant_dropdwon').find('option:selected').data('dropdownprice'));
    let originalPrice = 0;

    let discount_available = $('#variable_product .calculated_price').data('discount-available');
    // if (discount_available == true) {
        originalPrice = Number($('#variable_product .calculated_price').data('single-old-price'));
    // }else{
    //     originalPrice = Number($('.calculated_price').data('calculated-price'));
    // }
    

    let additionalPrice = 0;

    $('.variant_input').each(function(key, val) {
        additionalPrice += Number($(this).attr('data-additional-price'));
    });

    let calculatedPrice = originalPrice + additionalPrice;
    if (discount_available == true) {
        $('#variable_product .single-old-price').html('<del>BDT '+calculatedPrice+'</del>');
        let discount_type = $('.calculated_price').data('discount-type');
        let discount_amount = $('.calculated_price').data('discount-price');
        if(Number(discount_type) == 1){
            calculatedPrice = calculatedPrice - Number(discount_amount);
        }else{
            let discount = calculatedPrice * (Number(discount_amount) / 100);
            calculatedPrice = calculatedPrice - discount;
        }
    }

    let isSubmitAble = true;
    if(calculatedPrice < 0){
        isSubmitAble = false;
    }
    $('.price_text').text(calculatedPrice);
    $('.qty').attr('data-qty', 1);
    $('.qty').text(1);
    $('.qtyInput').val(1);
    let skuHtml = '';

    $('.variant_input').each(function() {
        let currentQty = $(this).attr('data-additional-qty');
        skuHtml = skuHtml.concat(" ", $(this).attr('data-additional-sku'));
        jQuery('.variable_sku').val(skuHtml);
        if (currentQty == -1) {
            isSubmitAble = false;
        }
    });


   // console.log(isSubmitAble);
    if (isSubmitAble) {
        $('.single_page_sku').html(skuHtml);
        $('.add_buy_variable button').removeClass('disabled');
        $('.add_buy_variable button').removeAttr('disabled');
        $('.add_buy_variable button').attr('type', 'submit');
    }else{
        $('.add_buy_variable button').addClass('disabled');
        $('.add_buy_variable button').attr('disabled',true);
        $('.add_buy_variable button').attr('type', 'button');
    }
}

// changePrice();

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