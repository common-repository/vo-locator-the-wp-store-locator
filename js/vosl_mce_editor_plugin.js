// JavaScript Document
(function() {
    tinymce.PluginManager.add('vosl_custom_mce_button', function(editor, url) {
        editor.addButton('vosl_custom_mce_button', {
            text: '',
			tooltip: "Insert VO Locator Shortcode",
            icon: 'vosl-custom-mce-button',
            onclick: function() {
                editor.insertContent('[VO-LOCATOR]');
            }
        });
    });
})();