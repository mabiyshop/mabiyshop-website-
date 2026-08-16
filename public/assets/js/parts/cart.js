$(document).on('click', '.single_shipping', function() {
    $(this).find('.shipping_input').attr('checked', true);
    $(this).closest('.cartShippingOption').find('.selected_shipping').text($(this).find('.shipping_title').text());
});


$(document).on('click', '.change_shipping_btn', function() {
    $(this).closest('.cartShippingOption').find('.single_product_shipping').slideToggle();
});

$(document).on('mouseleave', '.cart_product_group', function() {
    $(this).find('.single_product_shipping').slideUp();
});


