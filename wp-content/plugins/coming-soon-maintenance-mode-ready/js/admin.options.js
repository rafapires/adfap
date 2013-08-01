var cspAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(cspAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	jQuery('#cspAdminOptionsTabs').tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    jQuery( "#cspAdminOptionsTabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	
	jQuery('#cspAdminOptionsForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspAdminMainOptsMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					changeModeOptionCsp( jQuery('#cspAdminOptionsForm [name="opt_values[mode]"]').val() );
				}
			}
		});
		return false;
	});
	jQuery('#cspAdminOptionsSaveMsg').submit(function(){
		return false;
	});
	jQuery('.cspSetTemplateOptionButton').click(function(){
		jQuery('#cspAdminTemplatesSelection').dialog({
			modal:	true
		,	position: { my: 'center', at: 'center', of: window }
		,	width: jQuery(document).width() * 0.9
		,	minWidth: 400
		,	minHeight:	350
		,	open: function() {
				jQuery('.ui-widget-overlay').bind('click', function() {
					jQuery('#cspAdminTemplatesSelection').dialog('close');
				})
			}
		});
		return false;
	});
	jQuery('.cspGoToTemplateTabOptionButton').click(function(){
		// Go to tempalte options tab
		var index = jQuery('#cspAdminOptionsTabs a[href="#cspTemplateOptions"]').parents('li').index();
		jQuery('#cspAdminOptionsTabs').tabs('select', index);
		return false;
	});
	jQuery('#cspAdminOptionsForm [name="opt_values[mode]"]').change(function(){
		changeModeOptionCsp( jQuery(this).val(), true );
	});
	changeModeOptionCsp( toeOptionCsp('mode') );
	selectTemplateImageCsp( toeOptionCsp('template') );
	
	jQuery('.cspAdminTemplateOptRow').not('.cspAvoidJqueryUiStyle').buttonset();
	
	jQuery('#cspAdminTemplateOptionsForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspAdminTemplateOptionsMsg'
		});
		return false;
	});
	jQuery('#cspAdminTemplateOptionsForm [name="opt_values[bg_type]"]').change(function(){
		changeBgTypeOptionCsp();
	});
	changeBgTypeOptionCsp();
	
	 jQuery('.cspOptTip').live('mouseover',function(event){
        if(!jQuery('#cspOptDescription').attr('toeFixTip')) {
			var pageY = event.pageY - jQuery(window).scrollTop();
			var pageX = event.pageX;
			var tipMsg = jQuery(this).attr('tip');
			var moveToLeft = jQuery(this).hasClass('toeTipToLeft');	// Move message to left of the tip link
			if(typeof(tipMsg) == 'undefined' || tipMsg == '') {
				tipMsg = jQuery(this).attr('title');
			}
			toeOptShowDescriptionCsp( tipMsg, pageX, pageY, moveToLeft );
			jQuery('#cspOptDescription').attr('toeFixTip', 1);
		}
        return false;
    });
    jQuery('.cspOptTip').live('mouseout',function(){
		toeOptTimeoutHideDescriptionCsp();
        return false;
    });
	jQuery('#cspOptDescription').live('mouseover',function(e){
		jQuery(this).attr('toeFixTip', 1);
		return false;
    });
	jQuery('#cspOptDescription').live('mouseout',function(e){
		toeOptTimeoutHideDescriptionCsp();
		return false;
    });
	
	jQuery('#cspColorBgSetDefault').click(function(){
		jQuery.sendFormCsp({
			data: {page: 'options', action: 'setTplDefault', code: 'bg_color', reqType: 'ajax'}
		,	msgElID: 'cspAdminOptColorDefaultMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.newOptValue) {
						jQuery('#cspAdminTemplateOptionsForm [name="opt_values[bg_color]"]')
							.val( res.data.newOptValue )
							.css('background-color', res.data.newOptValue);
					}
				}
			}
		});
		return false;
	});
	jQuery('#cspImgBgSetDefault').click(function(){
		jQuery.sendFormCsp({
			data: {page: 'options', action: 'setTplDefault', code: 'bg_image', reqType: 'ajax'}
		,	msgElID: 'cspAdminOptImgBgDefaultMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.newOptValue) {
						jQuery('#cspOptBgImgPrev').attr('src', res.data.newOptValue);
					}
				}
			}
		});
		return false;
	});
	jQuery('#cspImgBgRemove').click(function(){
		if(confirm(toeLangCsp('Are you sure?'))) {
			jQuery.sendFormCsp({
				data: {page: 'options', action: 'removeBgImg', reqType: 'ajax'}
			,	msgElID: 'cspAdminOptImgBgDefaultMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#cspOptBgImgPrev').attr('src', '');
					}
				}
			});
		}
		return false;
	});
	jQuery('#cspLogoSetDefault').click(function(){
		jQuery.sendFormCsp({
			data: {page: 'options', action: 'setTplDefault', code: 'logo_image', reqType: 'ajax'}
		,	msgElID: 'cspAdminOptLogoDefaultMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.newOptValue) {
						jQuery('#cspOptLogoImgPrev').attr('src', res.data.newOptValue);
					}
				}
			}
		});
		return false;
	});
	jQuery('#cspLogoRemove').click(function(){
		if(confirm(toeLangCsp('Are you sure?'))) {
			jQuery.sendFormCsp({
				data: {page: 'options', action: 'removeLogoImg', reqType: 'ajax'}
			,	msgElID: 'cspAdminOptLogoDefaultMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#cspOptLogoImgPrev').attr('src', '');
					}
				}
			});
		}
		return false;
	});
	jQuery('#cspMsgTitleSetDefault').click(function(){
		jQuery.sendFormCsp({
			data: {page: 'options', action: 'setTplDefault', code: 'msg_title_params', reqType: 'ajax'}
		,	msgElID: 'cspAdminOptMsgTitleDefaultMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.newOptValue) {
						if(res.data.newOptValue.msg_title_color)
							jQuery('#cspAdminTemplateOptionsForm [name="opt_values[msg_title_color]"]')
								.val( res.data.newOptValue.msg_title_color )
								.css('background-color', res.data.newOptValue.msg_title_color);
						if(res.data.newOptValue.msg_title_font)
							jQuery('#cspAdminTemplateOptionsForm [name="opt_values[msg_title_font]"]').val(res.data.newOptValue.msg_title_font);
					}
				}
			}
		});
		return false;
	});
	jQuery('#cspMsgTextSetDefault').click(function(){
		jQuery.sendFormCsp({
			data: {page: 'options', action: 'setTplDefault', code: 'msg_text_params', reqType: 'ajax'}
		,	msgElID: 'cspAdminOptMsgTextDefaultMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					if(res.data.newOptValue) {
						if(res.data.newOptValue.msg_text_color)
							jQuery('#cspAdminTemplateOptionsForm [name="opt_values[msg_text_color]"]')
								.val( res.data.newOptValue.msg_text_color )
								.css('background-color', res.data.newOptValue.msg_text_color);
						if(res.data.newOptValue.msg_text_font)
							jQuery('#cspAdminTemplateOptionsForm [name="opt_values[msg_text_font]"]').val(res.data.newOptValue.msg_text_font);
					}
				}
			}
		});
		return false;
	});
	// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
	var formsPreventLeave = ['cspAdminOptionsForm', 'cspAdminTemplateOptionsForm', 'cspSubAdminOptsForm', 'cspAdminSocOptionsForm'];
	jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormCsp(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
		var formId = jQuery(this).parents('form:first').attr('id');
		changeAdminFormCsp(formId);
	});
	jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
		if(cspAdminFormChanged.length) {
			var id = jQuery(this).attr('id');
			for(var i in cspAdminFormChanged) {
				if(cspAdminFormChanged[i] == id) {
					cspAdminFormChanged.pop(i);
				}
			}
		}
	});
});
function changeAdminFormCsp(formId) {
	if(jQuery.inArray(formId, cspAdminFormChanged) == -1)
		cspAdminFormChanged.push(formId);
}
function changeModeOptionCsp(option, ignoreChangePanelMode) {
	jQuery('.cspAdminOptionRow-template, .cspAdminOptionRow-redirect, .cspAdminOptionRow-sub_notif_end_maint').hide();
	switch(option) {
		case 'coming_soon':
			jQuery('.cspAdminOptionRow-template').show( CSP_DATA.animationSpeed );
			break;
		case 'redirect':
			jQuery('.cspAdminOptionRow-redirect').show( CSP_DATA.animationSpeed );
			break;
		case 'disable':
			jQuery('.cspAdminOptionRow-sub_notif_end_maint').show( CSP_DATA.animationSpeed );
			break;
	}
	if(!ignoreChangePanelMode) {
		// Determine should we show Comin Soon sign in wordpress admin panel or not
		if(option == 'disable' && !jQuery('#wp-admin-bar-comingsoon').hasClass('cspHidden'))
			jQuery('#wp-admin-bar-comingsoon').addClass('cspHidden');
		else if(option != 'disable' && jQuery('#wp-admin-bar-comingsoon').hasClass('cspHidden'))
			jQuery('#wp-admin-bar-comingsoon').removeClass('cspHidden');
	}
}
function setTemplateOptionCsp(code) {
	jQuery.sendFormCsp({
		data: {page: 'options', action: 'save', opt_values: {template: code}, code: 'template', reqType: 'ajax'}
	,	msgElID: jQuery('.cspAdminTemplateShell-'+ code).find('.cspAdminTemplateSaveMsg')
	,	onSuccess: function(res) {
			if(!res.error) {
				selectTemplateImageCsp(code);
				if(res.data && res.data.new_name) {
					jQuery('.cspAdminTemplateSelectedName').html(res.data.new_name);
				}
			}
		}
	})
	return false;
}
function selectTemplateImageCsp(code) {
	jQuery('.cspAdminTemplateShell').removeClass('cspAdminTemplateShellSelected');
	if(code) {
		jQuery('.cspAdminTemplateShell-'+ code).addClass('cspAdminTemplateShellSelected');
	}
}
function changeBgTypeOptionCsp() {
	jQuery('#cspBgTypeStandart-selection, #cspBgTypeColor-selection, #cspBgTypeImage-selection').hide();
	if(jQuery('#cspAdminTemplateOptionsForm [name="opt_values[bg_type]"]:checked').size())
		jQuery('#'+ jQuery('#cspAdminTemplateOptionsForm [name="opt_values[bg_type]"]:checked').attr('id')+ '-selection').show( CSP_DATA.animationSpeed );
}
/* Background image manipulation functions */
function toeOptImgCompleteSubmitNewFile(file, res) {
    toeProcessAjaxResponseCsp(res, 'cspOptImgkMsg');
    if(!res.error) {
        toeOptImgSetImg(res.data.imgPath);
    }
}
function toeOptImgOnSubmitNewFile() {
    jQuery('#cspOptImgkMsg').showLoaderCsp();
}
function toeOptImgSetImg(src) {
	jQuery('#cspOptBgImgPrev').attr('src', src);
}
/* Logo image manipulation functions */
function toeOptLogoImgCompleteSubmitNewFile(file, res) {
    toeProcessAjaxResponseCsp(res, 'cspOptLogoImgkMsg');
    if(!res.error) {
        toeOptLogoImgSetImg(res.data.imgPath);
    }
}
function toeOptLogoImgOnSubmitNewFile() {
    jQuery('#cspOptLogoImgkMsg').showLoaderCsp();
}
function toeOptLogoImgSetImg(src) {
	jQuery('#cspOptLogoImgPrev').attr('src', src);
}
