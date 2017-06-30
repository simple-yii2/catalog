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

	$(document).on('change', '#offerform-defaultdelivery', function() {
		if (this.checked) {
			$('.offer-delivery :checkbox').prop({'disabled': true, 'checked': true});
			$('.offer-delivery :text').prop('disabled', true).val('');
		} else {
			$('.offer-delivery [name$="[active]"]').trigger('change').prop('disabled', false);
		}
	});

	$(document).on('change', '.offer-delivery [name$="[active]"]', function() {
		var $active = $(this),
			active = this.checked;

		$active.closest('tr').find(':checkbox').not($active).each(function() {
			var $check = $(this);
			$check.prop('disabled', !active);
			$check.closest('td').find(':text').prop('disabled', !active || this.checked);
		});
	});

	$(document).on('change', '.offer-delivery-cost :checkbox, .offer-delivery-days :checkbox', function() {
		var $td = $(this).closest('td'),
			$text = $td.find(':text');
		$td.find('input:hidden').val(this.checked ? '1' : '0');
		$text.prop('disabled', this.checked);
		if (this.checked)
			$text.val('');
	});
});
