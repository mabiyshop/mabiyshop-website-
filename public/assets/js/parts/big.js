//ZOOM image
$(document).ready(function() {
    setTimeout(function() {
        $('.circle-1').css({ 'background': '#0093d9' });
        $('.circle-1-text').css({ 'color': '#0093d9' });
        $('.circle-1').html('<i class="fa fa-check" aria-hidden="true"></i>');
        $('#big-img').css({ 'background': '#fff' });
        $('[id=\'big-img\']').css({ 'background': '#fff' });
        $('.zoom-show').zoomImage();
        $('.show-small-img:first-of-type').css({ 'border': 'solid 1px #951b25', 'padding': '2px' });
        $('.show-small-img:first-of-type').attr('alt', 'now').siblings().removeAttr('alt');
        $('.show-small-img').click(function() {
            $('#show-img').attr('src', $(this).attr('src'));
            $('[id=\'big-img\']').attr('src', $(this).attr('src'));
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

    }, 500);
});