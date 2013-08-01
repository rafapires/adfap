jQuery(document).ready(function(){
    jQuery('#subscribe_email').click(function(){
        jQuery('.cspFormHint').fadeIn('fast');
    });
    jQuery('#subscribe_email').focusout(function(){
        jQuery('.cspFormHint').fadeOut('fast');
    });
    jQuery('.cspHtmlLogo img').load(function() {
        jQuery('body').css('paddingTop', ((jQuery(window).height() - jQuery('.cspHtmlContainerWrapper').height()-40)/2));
    });
    jQuery('body').css('paddingTop', ((jQuery(window).height() - jQuery('.cspHtmlContainerWrapper').height()-40)/2));
}); 