jQuery(document).ready(function(){
	jQuery('#cspAdminMetaOptionsForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspAdminMetaOptionsMsg'
		});
		return false;
	});
});
