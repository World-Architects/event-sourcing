/**
 * This is a projection for the https://eventstore.org/ version 5
 *
 * The projection will check any events meta data for a field, by default called
 * _userId and if it is present, it will link the event to a stream called
 * UserAdit-<userId>. This projection will allow you to build very easy an
 * activity log based on events that users triggered.
 */
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
			// log('Linked: ' + streamId)
		}
	}
})
.outputState()
