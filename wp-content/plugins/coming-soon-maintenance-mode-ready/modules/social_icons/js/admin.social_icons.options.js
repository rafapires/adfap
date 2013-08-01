jQuery(document).ready(function(){
	jQuery('#cspAdminSocOptionsForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspAdmiSocOptionsMsg'
		});
		return false;
	});
});
