$(function() {
	$(document).on('change', '#offerform-category_id', function() {
		var $form = $(this).closest('form');

		$.post($form.data('propertiesUrl'), $form.serialize(), function(data) {
			$form.find('.properties').replaceWith($(data).find('.properties'));
		});
	});

	$(document).on('change', '#offerform-currency_id', function() {
		$('#offerform-price, #offerform-oldprice').prop('disabled', $(this).val() == '');
	});
});
