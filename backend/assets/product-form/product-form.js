$(function() {

	$(document).on('change', '#productform-category_id', function() {
		var $form = $(this).closest('form');

		$.post($form.data('propertiesUrl'), $form.serialize(), function(data) {
			$form.find('.properties').replaceWith($(data).find('.properties'));
		});
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
			var $tbody = $('#product-recommended tbody'),
				$tr = $(data.content).find('#product-recommended tbody tr');

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
