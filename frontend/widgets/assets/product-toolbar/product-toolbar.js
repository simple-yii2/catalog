$(document).on('show.bs.modal', '.product-toolbar-filter-modal', function() {
    var $this = $(this), $filter = $($this.data('target'));
    $this.data('parent', $filter.parent()).find('.modal-body').append($filter);
});
$(document).on('hidden.bs.modal', '.product-toolbar-filter-modal', function() {
    var $this = $(this);
    $this.find($this.data('target')).appendTo($this.data('parent'));
});
$(document).on('click', '.product-toolbar-filter-modal .modal-header', function() {
    $('.product-toolbar-filter-modal').modal('hide');
});



$(document).on('click', '.product-toolbar-filter button', function(e) {
    $('.product-toolbar-filter-modal').modal();
});
