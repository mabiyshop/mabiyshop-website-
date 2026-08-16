$(document).on('click', '#address_list', function() {
    $('#list').show();
    $('#add').hide();
});

$(document).on('click', '#address_add', function() {
    $('#list').hide();
    $('#add').show();
});