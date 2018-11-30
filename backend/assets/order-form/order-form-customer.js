$('#orderform-customer-user').on('autocompleteselect', function(e, ui) {
    $(this).data('value', ui.item['value']);
    $('#orderform-customer-user_id').val(ui.item['id']);
    $('#orderform-customer-name').val(ui.item['name']);
    $('#orderform-customer-email').val(ui.item['email']);
}).on('blur', function() {
    var $this = $(this);
    $this.val($this.data('value'));
});
