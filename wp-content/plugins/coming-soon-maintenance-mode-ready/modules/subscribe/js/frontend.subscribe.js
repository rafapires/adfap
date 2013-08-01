jQuery(document).ready(function(){
	jQuery('#cspSubscribeForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspSubscribeCreateMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#cspSubscribeForm').clearForm();
				}
			}
		});
		return false;
	});
});