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

	$(document).on('blur', '#recommended-add', recommendedAddBlur);
	$(document).on('keypress', '#recommended-add', recommendedAddKeypress);
	$(document).on('selected', '#recommended-add', recommendedAddSelected);
	$(document).on('click', '.recommended-remove', recommendedRemoveClick);

	function recommendedAddBlur()
	{
		$(this).val('');
	};

	function recommendedAddKeypress(e)
	{
		if (e.which == 13)
			e.preventDefault();
	};

	function recommendedAddSelected(e)
	{
		var $this = $(this);

		$.get($this.data('url'), {'recommended_id': $this.data('item').id}, function(data) {
			var $tbody = $('#offer-recommended tbody'),
				$tr = $(data.content).find('#offer-recommended tbody tr');

			//remove empty tr
			$tbody.find('tr > td > div.empty').parent().parent().remove();

			//add if not exists
			var $dest = $tbody.find('tr[data-id="' + $tr.data('id') + '"]');
			if ($dest.length == 0)
				$dest = $tr.appendTo($tbody);

			//highlight
			$dest.effect('highlight', {}, 1000);
		}, 'json');
	}

	function recommendedRemoveClick(e)
	{
		e.preventDefault();
		$(this).closest('tr').remove();
	};

});
