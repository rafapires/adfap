(function() {
    tinymce.create('tinymce.plugins.toeShortCodesButtons', {
        init : function(ed, url) {
            ed.addButton('toeshortcodes', {
                title : toeLang('Ready! Shortcodes'),
                image : url+'/toeshortcodesbutton.png',
                onclick : function(event) {
                    subScreen.show(toeShortcodesText.adminTextEditorPopup)
						.moveToCenter()
						.setAbsolute();
                    toeTextEditorInst = ed;
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : 'ReadyShoppingCart.com - Shortcodes',
                author : 'readyshoppingcart.com',
                authorurl : 'http://readyshoppingcart.com/',
                infourl : 'http://readyshoppingcart.com/',
                version : '1.0'
            };
        }
    });
    tinymce.PluginManager.add('toeshortcodes', tinymce.plugins.toeShortCodesButtons);
})();