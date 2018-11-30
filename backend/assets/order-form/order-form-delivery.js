$('#orderform-delivery-delivery').on('change', function() {
    var $block = $('#delivery-block'), a, $fields, $available, delivery;

    //delivery data
    var id = $(this).val();
    delivery = $block.data('delivery')[id];
    if (typeof delivery == 'undefined') {
        delivery = {'price': '', 'days': '', 'fields': []};
    }

    //set values
    $('#orderform-delivery-price').val(delivery['price']).trigger('change');
    $('#orderform-delivery-days').val(delivery['days']);

    //fields
    //all
    a = [];
    $.each($block.data('fields'), function(i, v) {
        a.push('.field-orderform-delivery-' + v.toLowerCase());
    });
    $fields = $(a.join(','));
    //available
    a = [];
    $.each(delivery.fields, function(i, v) {
        a.push('.field-orderform-delivery-' + v.toLowerCase());
    });
    $available = $(a.join(','));
    //show available
    $fields.not($available).addClass('hidden');
    $available.removeClass('hidden');
});
