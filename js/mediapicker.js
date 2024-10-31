(function ($) {

	var custom_uploader;

	$("input:button[name=mediaselect]").click(function(e) {
		var sid = this.id;
		sid = sid.slice(sid.indexOf('-', 1));

	    e.preventDefault();

		var language = (window.navigator.userLanguage || window.navigator.language || window.navigator.browserLanguage).substr(0,2) == "ja" ? "ja" : "en";
		if (language == "ja") {
			var msg = "画像を選択";
		} else {
			var msg = "Choose Image";
		}

		custom_uploader = wp.media({
		    title: msg,
		    library: { type: "image" },		/* 画像のみ */
		    button: { text: msg },
		    multiple: false					/* 複数選択なし */
		});
		custom_uploader.on("select", function() {
			var images = custom_uploader.state().get("selection");
			images.each(function(file){
				document.getElementById("url"+sid).value = "";					// クリア
				document.getElementById("url"+sid).value = file.toJSON().url;		// 画像URL
				document.getElementById("img"+sid).src = "";
				document.getElementById("img"+sid).src = file.toJSON().url;
			});
		});

	    custom_uploader.open();
	});

})(jQuery);