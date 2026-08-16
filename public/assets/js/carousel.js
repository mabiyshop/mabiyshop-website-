//Carousel 
$(document).on('click', '.rightlst', function(e) {
    e.stopImmediatePropagation();
    var mrl = Number($(this).attr("data-mrMinus")) + 240;
    var totalwidth = Number($(this).attr("data-carouseWith"));
    var deferent = totalwidth - 1140;
	if(deferent < mrl){
        $(this).attr("data-mrMinus", 0);
         var mrl = 0;
    }
    $(this).attr("data-mrMinus", mrl);
    $(this).closest('.mymultipleslider').find('.rescarousel-inner').css({'margin-left':'-'+mrl+'px','transition':'0.5s'});
});
$(document).on('click', '.leftlst', function(e) {
    e.stopImmediatePropagation();
    var mrl = Number($(this).closest('.carousel_btn').find('.rightlst').attr("data-mrMinus")) - 240;
    if(mrl < 1){
        var mrl = 0;
    }
    $('.rightlst').attr("data-mrMinus", mrl);
    $(this).closest('.mymultipleslider').find('.rescarousel-inner').css({
        'margin-left':'-'+mrl+'px', 
        'transition':'0.5s'
    });
});


$('#loginModal').on('hidden.bs.modal', function () {
    $('.buyNow').attr('disabled', false);
    $('.buyNow').html('Buy Now');
    $('.buy_now').attr('disabled', false);
    $('.buy_now').html('Buy Now');
    $('.addToCart').attr('disabled', false);
    $('.addToCart').html('Add To Cart');
    $('.add_to_cart').attr('disabled', false);
    $('.add_to_cart').html('Add To Cart');
});

