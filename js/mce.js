jQuery(function($){
	$(function() {
	    // TinyMCEにモーダルを開くボタンを追加
		tinymce.create("tinymce.plugins.pztalk", {
			init: function(ed, url){
				ed.addButton("pz_talk",{
					title: "Insert talk",
					image: url + "/button.png",
					cmd: "insert_pz_talk"
				});
				ed.addCommand("insert_pz_talk", function() {
					$("#pz-talk-open").click();
				});
			},
			createControl: function(n, cm) {
				return null;
			}
		});
		tinymce.PluginManager.add("pz_talk",tinymce.plugins.pztalk);
		tinymce.PluginManager.requireLangPack('pz_talk');
	});
});
