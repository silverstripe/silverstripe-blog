(function($) {
$.entwine('ss', function($){

	$('#BBCodeHint').entwine({
		onclick: function() {
			$('#BBTagsHolder').toggle();
		}
	});

});
}(jQuery));

