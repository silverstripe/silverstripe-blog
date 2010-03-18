Behaviour.register({
	'#BBCodeHint': {
		onclick: function() {
			if($('BBTagsHolder').style.display == "none") {
				Effect.BlindDown('BBTagsHolder');
			} else{
				Effect.BlindUp('BBTagsHolder');
			}
			return false;
		}
	}
});
