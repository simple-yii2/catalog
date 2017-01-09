$(function() {
	$(document).on('change', '#goodsform-category_id', function() {
		var $form = $(this).closest('form');

		$.post($form.data('propertiesUrl'), $form.serialize(), function(data) {
			$form.find('.properties').replaceWith($(data).find('.properties'));
		});
	});
});
