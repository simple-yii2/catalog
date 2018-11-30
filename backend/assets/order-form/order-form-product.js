// Add product
$('.product-add').on('click', function() {
    var $tbody = $('#product-list tbody'), $tmp = $tbody.find('tr:last'), $tr = $tmp.clone(), idx = 0;

    // Index for inputs names
    if ($tbody.find('tr').length > 1) {
        idx = parseInt($tmp.prev().find('input:first').attr('name').match(/\[(\d+)\]/)[1]) + 1;
        console.log(idx);
    }
    $tr.find('input').each(function () {
        this.name = this.name.replace('[]', '[' + idx + ']');
        $(this).prop('disabled', false);
    });

    // Name autocomplete
    $tr.find('.p-name').autocomplete({source: function (request, response) {
        request['currency_id'] = $('#orderform-currency_id').val();
        $.get($('#product-list').data('urlProduct'), request, function (data) {
            response(data);
        }, 'json');
    }});

    $tr.insertBefore($tmp);
});

// Remove product
$(document).on('click', '.product-remove', function (e) {
    e.preventDefault();
    var $tr = $(this).closest('tr'), $form = $tr.closest('form');
    $tr.remove();
    $form.trigger('calc');
});

// Name autocomplete
$(document).on('autocompleteselect', '#orderform-products-name', function(e, ui) {
    var $this = $(this), $tr = $this.closest('tr');
    $this.data('value', ui.item['value']).attr('title', ui.item['value']);
    $tr.find('#orderform-products-product_id').val(ui.item['id']);
    $tr.find('#orderform-products-sku').val(ui.item['sku']);
    $tr.find('#orderform-products-price').val(ui.item['price']);
    $tr.closest('form').trigger('calc');
}).on('blur', '#orderform-products-name', function() {
    var $this = $(this);
    $this.val($this.data('value'));
});

// Price, count and discount changes
$(document).on('change', '#orderform-products-count, #orderform-products-price, #orderform-products-discount', function () {
    $(this).closest('tr').trigger('calc');
});

// Calc product
// $(document).on('calc', '#product-list tr', function () {
//     var $tr = $(this);

//     // Clear amount fields
//     $tr.find('.p-amount, .p-discountamount, .p-totalamount').text('');

//     // Calculate
//     var data = {};
//     $tr.find('input').each(function () {
//         data[this.name.match(/\[(\w+)\]$/)[1]] = $(this).val();
//     });
//     $.get($('#product-list').data('urlCalc'), data, function (data) {
//         if (data.success) {
//             // Field values
//             $tr.find('.p-amount').html(data['amount']);
//             $tr.find('.p-discountamount').html(data['discountAmount']);
//             $tr.find('.p-totalamount').html(data['totalAmount']);
//         }
//     }, 'json');
// });
