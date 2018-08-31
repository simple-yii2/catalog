$(document).on('click', '.product-filter .filter-title', titleClick);
$(document).on('change', '.product-filter .filter-range .filter-controls input', rangeChange);
$(document).on('change', '.product-filter .filter-select .filter-controls input', selectChange);

function titleClick(e) {
    e.preventDefault();
    $(this).closest('.filter-item').toggleClass('expanded');
};

function rangeChange(e) {
    var $controls = $(this).closest('.filter-controls'),
        from = $controls.find('input:first').val(),
        to = $controls.find('input:last').val(),
        val = from + '_' + to;

    if (val == '_') {
        val = '';
    };

    $controls.closest('.filter-range').find('input:hidden').val(val).prop('disabled', val == '');
};

function selectChange(e) {
    var $controls = $(this).closest('.filter-controls'),
        a = [], val;

    $controls.find('input:checked').each(function() {
        a.push($(this).val());
    });

    val = a.join('_');

    $controls.closest('.filter-select').find('input:hidden').val(val).prop('disabled', val == '');
};
