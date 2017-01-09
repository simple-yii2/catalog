$(function() {
	$(document).on('change', '.property-boolean input', function() {
		var $this = $(this);

		if (this.checked) {
			var $input = $(this).closest('.property-boolean').find('input').not(this);

			if ($input[0].checked)
				$input.parent().button('toggle');
		};
	});
});
