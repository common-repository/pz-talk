$(function() {
    // 画面のどこかをクリックしたらモーダルを閉じる
    $("#pz-talk-overlay,#pz-talk-close").unbind().click(function(){
        $("#pz-talk-overlay").css("display", "none");
        $("#pz-talk-modal").css("display"," none");
        $("#pz-talk-serif").val("");
		$("#pz-talk-check").prop("checked", false);
    });

	// [ESC]キーが押されたらCLOSEをクリック
	$(document).keydown(function(e) {
		if (e.keyCode == 27) {
			$("#pz-talk-close").click();
		}
	});

	$("#pz-talk-serif").keydown(function(e) {
		if (e.keyCode == 38 || e.keyCode == 40) {
			var s = $("#pz-talk-name").prop("selectedIndex");
			if (e.keyCode == 40) {
				if (s < $("#pz-talk-name").children().length - 1) {
					s++;
				}
			}
			if (e.keyCode == 38) {
				if (s > 0) {
					s--;
				}
			}
			$("#pz-talk-name").prop("selectedIndex", s);
			$("#pz-talk-icon").prop("selectedIndex", s);
			return false;
		}
	});

	// 名前の変更
	$("#pz-talk-name").change(function(){
		$("#pz-talk-icon").val($(this).val());
	});

    // 挿入ボタン
    $("#pz-talk-insert").unbind().click(function(){
    	var sc = "<p>[" + $("#pz-talk-code").val() + " name=\"" + $("#pz-talk-name").val() + "\" ";
    	if ($("#pz-talk-name").val() != $("#pz-talk-icon").val()) {
    		sc = sc + "icon=\"" + $("#pz-talk-icon").val() + "\" ";
        }
    	sc = sc + "]" + $("#pz-talk-serif").val() + "[/" + $("#pz-talk-code").val() + "]</p>";
        $("#pz-talk-overlay").css("display","none");
        $("#pz-talk-modal").css("display","none");
        tinymce.activeEditor.selection.setContent(sc);
        tinymce.activeEditor.focus()
        $("#pz-talk-serif").val("");
		$("#pz-talk-check").prop("checked", false);
    });

	// アイコンのチェック
    $("#pz-talk-check").unbind().click(function(){
    	if ($("#pz-talk-check").prop("checked") == true) {
    		$("#pz-talk-icon").prop("disabled", false);
    	} else {
    		$("#pz-talk-icon").prop("disabled", true);
    	}
    });

    // ウィンドウのリサイズ
    $(window).resize(centermodal);
    function centermodal() {
        var w = $(window).width();
        var h = $(window).height();
        var mw = $("#pz-talk-modal").outerWidth();
        var mh = $("#pz-talk-modal").outerHeight();
        $("#pz-talk-modal").css( {"left": ((w - mw)/2) + "px","top": ((h - mh)/2) + "px"} );
    }

    // TinyMCEにモーダルを開くボタンを追加
	tinymce.create("tinymce.plugins.pztalk", {
		init: function(ed, url){
			ed.addButton("pz_talk",{
				title: "Insert talk",
				image: url + "/button.png",
				cmd: "insert_pz_talk"
			});
			ed.addCommand("insert_pz_talk", function() {
                $("#pz-talk-overlay").css("display", "block");
                $("#pz-talk-modal").css("display", "block");
                var st = tinymce.activeEditor.selection.getContent();
                var my = st.match(/name="([^"]*)"/);
                if (my != null) {
					my = my[1];
					$("#pz-talk-name").val(my);
					$("#pz-talk-icon").val(my);
                }
                var ic = st.match(/icon="([^"]*)"/);
                if (ic != null) {
					ic = ic[1];
					if (my != ic) {
						$("#pz-talk-check").prop("checked", true);
						$("#pz-talk-icon").prop("disabled", false);
						$("#pz-talk-icon").val(ic);
					}
                }
                st = st.replace(/\[[^\]]*\]/g, "");
                st = st.replace(/<\/*p>/g, "");
                $("#pz-talk-serif").val(st);
                $("#pz-talk-serif").focus();
                centermodal();
			});
		},
		createControl: function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add("pz_talk",tinymce.plugins.pztalk);
	tinymce.PluginManager.requireLangPack('pz_talk');
})();