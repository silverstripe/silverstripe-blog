/**
 * Register expandable help text functions with fields.
 */
(function ($) {

	$.entwine('ss', function ($) {

		$('.toggle-description').entwine({
			'onadd': function () {
				var $this = $(this);

				/**
				 * Prevent multiple events being added.
				 */
				if ($this.hasClass('toggle-description-enabled')) {
					return;
				}

				$this.addClass('toggle-description-enabled');

				/**
				 * Toggle next description when button is clicked.
				 */
				var shown = false;

				$this.on('click', function() {
					$this.parent().next('.description')[shown ? 'hide' : 'show']();

					$this.toggleClass('toggle-description-shown');

					shown = !shown;
				});

				/**
				 * Hide next description by default.
				 */
				$this.parent().next('.description').hide();

				/**
				 * Add classes to correct inherited layout issues in a small context.
				 */
				$this.parent().addClass('toggle-description-correct-right');
				$this.parent().prev('.middleColumn').addClass('toggle-description-correct-middle');
				$this.parent().next('.description').addClass('toggle-description-correct-description');
			}
		});

	});
})(jQuery);
