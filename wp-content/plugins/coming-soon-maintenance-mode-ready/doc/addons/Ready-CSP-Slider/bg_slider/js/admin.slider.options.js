jQuery(document).ready(function(){
	jQuery('#cspAdminTemplateOptionsForm [name="slider_enabled_checkbox"]').change(function(){
		changeSliderEnableOptionCsp();
	});
	changeSliderEnableOptionCsp();
	
	// TODO: define cspSlides variable
	toeOptSlidesRedraw( cspSlides, cspSlidesNames /*Defined in template file*/ );
	
	jQuery('#cspAdminSlidesListShell').sortable({
		update: function(event, ui) {
			var newImagesArray = [];
			jQuery('#cspAdminSlidesListShell .cspAdminSlideShell').not('.cspExample').each(function(){
				newImagesArray.push( jQuery(this).find('img:first').attr('original') );
			});
			if(newImagesArray.length) {
				jQuery.sendFormCsp({
					msgElID: 'cspOptSlideImgMsg'
				,	data: {page: 'options', action: 'save', reqType: 'ajax', code: 'slider_images', opt_values: {slider_images: newImagesArray}}
				});
			}
		}
	});
});

function changeSliderEnableOptionCsp() {
	var enabled = jQuery('#cspAdminTemplateOptionsForm [name="slider_enabled_checkbox"]').attr('checked')
	,	isVisible = jQuery('#cspAdminOptionsSlides').is(':visible');
	if(!enabled && isVisible) {
		jQuery('#cspAdminOptionsSlides').hide();
		jQuery('#cspAdminTemplateOptionsForm [name="opt_values[slider_enabled]"]').val('0');
	} else if(enabled && !isVisible) {
		jQuery('#cspAdminOptionsSlides').show( CSP_DATA.animationSpeed );
		jQuery('#cspAdminTemplateOptionsForm [name="opt_values[slider_enabled]"]').val('1');
	}
}
function toeOptSlideImgCompleteSubmitNewFile(file, res) {
    toeProcessAjaxResponseCsp(res, 'cspOptSlideImgMsg');
    if(!res.error) {
		toeOptSlidesRedraw(res.data.slides, res.data.slidesNames);
    }
}
function toeOptSlideImgOnSubmitNewFile() {
	jQuery('#cspOptSlideImgMsg').showLoaderCsp();
}
function toeOptSlidesRedraw(list, namesList) {
	jQuery('#cspAdminSlidesListShell .cspAdminSlideShell').not('.cspExample').remove();
	if(list.length) {
		for(var i in list) {
			var newShell = jQuery('#cspAdminSlidesListShell .cspExample').clone();
			jQuery(newShell).removeClass('cspExample').show().find('img:first').attr('src', list[i]).attr('original', namesList[i]);
			jQuery('#cspAdminSlidesListShell').append( newShell );
		}
	}
}
function cspRemoveSlide(remLink) {
	var container	= jQuery(remLink).parents('.cspAdminSlideShell:first')
	,	imgCode		= container.find('img:first').attr('original');
	if(container.size() && imgCode && imgCode != '') {
		jQuery.sendFormCsp({
			msgElID: 'cspOptSlideImgMsg'
		,	data: {page: 'bg_slider', action: 'removeSlide', reqType: 'ajax', imgCode: imgCode}
		,	onSuccess: function(res) {
				if(!res.error) {
					container.remove();
				}
			}
		})
	}
}
