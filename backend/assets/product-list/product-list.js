$('form select[name="category_id"]').on('change', function () {
    $(this).closest('form').submit();
});
