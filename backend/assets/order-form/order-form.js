$('#orderform-discount').on('change', function () {
    $(this).closest('form').trigger('calc');
});

$('#order-form').on('calc', function () {
    var $form = $(this);
    $.get($form.data('urlCalc'), $form.serialize(), function (data) {
        // Order discount
        $('#orderform-products-discount').attr('placeholder', data.discount);
        // Delivery methods
        // Products
        $.each(data.products, function (i, v) {
            var $tr = $('#product-list :input[name$="[product_id]"][value="' + v.product_id + '"]').closest('tr');
            $tr.find('.product-amount').html(v.amount);
            $tr.find('.product-discount-amount').html(v.discountAmount);
            $tr.find('.product-total-amount').html(v.totalAmount);
        });
        // Total
        $('.o-product-amount').html(data.productAmount);
        $('.o-discount-amount').html(data.discountAmount);
        $('.o-subtotal-amount').html(data.subtotalAmount);
    }, 'json');
});
