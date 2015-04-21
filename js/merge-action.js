/**
 * Register expandable help text functions with fields.
 */
(function ($) {

	$.entwine('ss', function ($) {

		$('.MergeAction').entwine({
			'onadd': function() {
				var $this = $(this);

				$this.on('click', 'select', function() {
					return false;
				});

				$this.children('button').each(function(i, button) {
					var $button = $(button);
					var $select = $button.prev('select');

					$button.before('<input type="hidden" name="' + $button.attr('data-target') + '" value="' + $select.val() + '" />');
				});

				$this.on('change', 'select', function(e) {
					var $target = $(e.target);

					$target.next('input').val($target.val());
				});

				$this.children('button, select').hide();

				$this.on('click', '.MergeActionReveal', function(e) {
					var $target = $(e.target);

					$target.parent().children('button, select').show();
					$target.hide();

					return false;
				});
			}
		})

	});

}(jQuery));
