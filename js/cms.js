(function ($) {

	$.entwine('ss', function ($) {

        /**
         * The page success/error message sits outside of the html block
         * containing the sidebar and cms fields. This means it overflows
         * underneath the sidebar.
         *
         * @see https://github.com/silverstripe/silverstripe-blog/issues/210
         */
        $('.cms-content-fields > #Form_EditForm_error').entwine({
            'onadd': function() {
                var $target = $('.blog-admin-outer');
                if($target.length == 1) {
                    $target.prepend(this);
                }
            }
        });

		/**
		 * Register expandable help text functions with fields.
		 */
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

		/**
		 * Custom merge actions for tags and categories
		 */
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
		});

		/**
		 * Customise the cms-panel behaviour for blog sidebar
		 *
		 * see LeftAndMain.Panel.js for base behaviour
		 */
		$('.blog-admin-sidebar.cms-panel').entwine({
			MinInnerWidth: 620,
			onadd: function() {
				this._super();
				this.updateLayout();

				// If this panel is open and the left hand column is smaller than the minimum, contract it instead
				if(!this.hasClass('collapsed') && ($(".blog-admin-outer").width() < this.getMinInnerWidth())) {
					this.collapsePanel();
				}

                window.onresize = function() {
                    this.updateLayout();
                }.bind(this);
			},
			togglePanel: function(bool, silent) {
				this._super(bool, silent);
				this.updateLayout();
			},
			/**
			 * Adjust minimum width of content to account for extra panel
			 *
			 * @returns {undefined}
			 */
			updateLayout: function() {
                $(this).css('height', '100%');
                var currentHeight = $(this).outerHeight();
                var bottomHeight = $('.cms-content-actions').eq(0).outerHeight();
                $(this).css('height', (currentHeight - bottomHeight) +  "px");
                $(this).css('bottom', bottomHeight + "px");

				$('.cms-container').updateLayoutOptions({
					minContentWidth: 820 + this.width()
				});

			}
		});

	});
})(jQuery);
