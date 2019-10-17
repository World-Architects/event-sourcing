var options = {
	prefix: 'UserAudit-',
	field: '_userId'
};

fromAll()
.when({
	$any:function (state, event) {
		if (event.metadata === null) {
			return;
		}

		if (event.metadata[options.field]) {
			var streamId = options.prefix + event.metadata[options.field];
			linkTo(streamId, event)
			log('Linked: ' + streamId)
		}
	}
})
.outputState()
