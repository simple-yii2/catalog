$(function() {

	$(document).on('change', '.category-properties .property-type', function() {
		var	$this = $(this),
			type = parseInt($this.val()),
			$button = $this.closest('tr').find('button.property-values'),
			$properties = $this.closest('.category-properties');

		$button.prop('disabled', $properties.data('typesWithValues').indexOf(type) == -1);
	});

	$(document).on('click', '.category-properties button.property-values', function() {
		var $this = $(this),
			$properties = $this.closest('.category-properties'),
			$modal = $('<div class="modal fade modal-properties" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"></h4></div><div class="modal-body"><table class="table table-condensed table-bordered"><colgroup><col></col><col style="width: 25px;"></col></colgroup><tbody></tbody></table></div><div class="modal-footer"><div class="pull-left"><button type="button" class="btn btn-default btn-modal-add"></button></div><button type="button" class="btn btn-primary btn-modal-save"></button><button type="button" class="btn btn-default btn-modal-cancel" data-dismiss="modal"></button></div></div></div></div>'),
			$table = $modal.find('table'),
			$input = $this.prevAll('.property-values');

		$modal.on('hidden.bs.modal', function() {
			$modal.remove();
		});

		$modal.data('input', $input);

		$modal.find('.modal-title').text($properties.data('modalTitle'));
		$modal.find('.btn-modal-add').text($properties.data('modalAdd')).on('click', addClick);
		$modal.find('.btn-modal-save').text($properties.data('modalSave')).on('click', saveClick);
		$modal.find('.btn-modal-cancel').text($properties.data('modalCancel'));

		$input.nextAll('input').each(function() {
			addTableRow($table, this.value);
		});

		$modal.modal();
	});

	function addClick() {
		var $table = $(this).closest('.modal').find('table');
		addTableRow($table, '');
	};

	function saveClick() {
		var $modal = $(this).closest('.modal'),
			$input = $modal.data('input'),
			name = $input[0].name + '[]';

		$input.nextAll('input').remove();

		$modal.find('input').each(function() {
			if (this.value != '')
				$input = $('<input type="hidden" />').attr('name', name).val(this.value).insertAfter($input);
		});

		$modal.modal('hide');
	};

	function addTableRow($table, value) {
		var $tr = $('<tr><td><input type="text" class="form-control" /></td><td><a href="#"><span class="glyphicon glyphicon-remove"></span></a></td></tr>');

		$tr.find('input').val(value);

		$table.find('tbody').append($tr);
	};

});
