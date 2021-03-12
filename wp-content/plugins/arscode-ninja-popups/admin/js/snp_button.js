jQuery(document).ready(function($) {
    tinymce.create('tinymce.plugins.snp_plugin', {
    	init: function(ed, url) {
    		ed.addCommand('snp_insert_shortcode', function() {
    			ed.windowManager.open({
    				file : ajaxurl+"?action=snp_insert_shortcode",
    				width : 380,
    				height : 130,
    				inline : 1
    			}, {
    				plugin_url : url,
    				selected: tinyMCE.activeEditor.selection.getContent()
    			});
    		});

    		ed.addButton('snp_button', {
    			title : 'Insert shortcode', 
    			cmd : 'snp_insert_shortcode', 
    			image: url + '/../img/snp_button.png'
    		});
    	}
    });

    tinymce.PluginManager.add('snp_button', tinymce.plugins.snp_plugin);
});