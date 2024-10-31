<?php
/*
Plugin Name: Pz-Talk
Plugin URI: https://popozure.info
Description: 顔アイコンと吹き出しを表示させて会話を表現できます。
Version: 1.1.2
Author: poporon
Author URI: https://popozure.info
*/
class Pz_Talk {
	public	$slug;
	public	$text_domain;		// as slug
	
	public	$plugin_basename;
	public	$plugin_dir_path;
	public	$plugin_dir_url;
	
	public $options;
	
	protected	$defaults = array(
			'code1'				=>	'talk',
			'code2'				=>	'message',
			'message-me-right'	=>	'1',
			'talk-me-right'		=>	null,
			'flg-edit-insert'	=>	'1',
			'flg-debug'			=>	null,
			'plugin-name'		=>	'Pz-Talk',
			'plugin-version'	=>	'1.1.2'
		);

	function __construct() {
		$this->slug = basename(dirname(__FILE__));
		$this->text_domain		= $this->slug;
		
		$this->plugin_basename	= plugin_basename(__FILE__);
		$this->plugin_dir_path	= plugin_dir_path(__FILE__);
		$this->plugin_dir_url	= plugin_dir_url (__FILE__);
		
		$this->options = get_option('Pz_Talk_options', $this->defaults );
		foreach ($this->defaults as $key => $value) {
			if (!isset($this->options[$key])) {
				$this->options[$key] = null;
			}
		}
		
		// 日本語化
		load_plugin_textdomain($this->text_domain, false, $this->slug.'/languages');
		
		// ショートコードの設定
		if ($this->options['code1']) {
			add_shortcode($this->options['code1'], array($this, 'shortcode1'));				// ショートコードの設定
		}
		if ($this->options['code2']) {
			add_shortcode($this->options['code2'], array($this, 'shortcode2'));				// ショートコードの設定
		}
		add_action('wp_enqueue_scripts', array($this, 'enqueue'));							// スクリプト・CSSの設定
		
		if ($this->options['flg-debug']) {
			add_action('wp_footer', array($this, 'footer'));								// デバッグ情報
		}
		
		// 管理画面のとき
		if (is_admin()) {
			add_action('admin_menu', array($this, 'add_menu') );											// 設定メニュー
			add_action('admin_print_scripts', array($this, 'admin_scripts') );								// スクリプト
			add_action('admin_print_footer_scripts', array($this, 'admin_footer') );						// テキストエディタ用クイックタグ
			add_filter('mce_buttons', array($this, 'add_mce_button') );										// ビジュアルエディタ用ボタン
			add_filter('mce_external_plugins', array($this, 'add_mce_plugin') );							// ビジュアルエディタ用ボタン
			add_filter('plugin_action_links_'.$this->plugin_basename, array($this, 'action_links') );		// プラグイン画面
		}
	}

	// ショートコード処理
	function shortcode1($opt , $content = null) {
		// デバッグ情報
		if ($this->options['flg-debug']) {
			echo PHP_EOL;
			echo '<!-- [Pz-Talk] Debug information ---'.PHP_EOL;
			echo print_r($opt, true);
			echo '--- [Pz-Talk] /-->'.PHP_EOL;
		}
		
		// 登場人物設定から情報を取得
		$opt		=	$this->pz_talk_getperson($opt);
		
		// 右寄せの指定
		$position	=	null;
		if	(isset($opt['position']) && (strtolower(substr($opt['position'], 0, 1)) == 'r') ) {
			$position	=	1;
		} elseif (isset($opt['pos']) && (strtolower(substr($opt['pos'], 0, 1)) == 'r') ) {
			$position	=	1;
		} elseif (isset($opt['subtype']) && (strtolower(substr($opt['subtype'], 0, 1)) == 'r') ) {
			$position	=	1;
		} elseif ($this->options['talk-me-right'] && $opt['me']) {
			$position	=	1;
		}
		
		// HTMLタグ
		if	($position) {
			$content	= '<div class="pz-talk-me"><figure class="pz-talk-face"><img src="'.$opt['url'].'" alt=""><figcaption class="pz-talk-name"><B>'.$opt['name'].'</B></figcaption></figure><div class="pz-talk-content-me"><div class="pz-talk-message-me">'.do_shortcode($content).'</div></div></div>';
		} else {
			$content	= '<div class="pz-talk"><figure class="pz-talk-face"><img src="'.$opt['url'].'" alt=""><figcaption class="pz-talk-name"><B>'.$opt['name'].'</B></figcaption></figure><div class="pz-talk-content"><div class="pz-talk-message">'.do_shortcode($content).'</div></div></div>';
		}
		return $content;
	}

	// ショートコード処理
	function shortcode2($opt , $content = null) {
		// デバッグ情報
		if ($this->options['flg-debug']) {
			echo PHP_EOL;
			echo '<!-- [Pz-Talk] Debug information ---'.PHP_EOL;
			echo print_r($opt, true);
			echo '--- [Pz-Talk] /-->'.PHP_EOL;
		}
		
		// 登場人物設定から情報を取得
		$opt		=	$this->pz_talk_getperson($opt);
		
		if	($opt['me']) {
			$content	= '<div class="pz-line-me"><div class="pz-line-content-me"><div class="pz-line-message-me">'.do_shortcode($content).'</div></div></div>';
		} else {
			$content	= '<div class="pz-line"><figure class="pz-line-face"><img src="'.$opt['url'].'" alt=""></figure><div class="pz-line-name">'.$opt['name'].'</div><div class="pz-line-content"><div class="pz-line-message">'.do_shortcode($content).'</div></div></div>';
		}
		return $content;
	}

	// 登場人物設定から情報を取得
	function pz_talk_getperson($opt) {
		// パラメータが空だったとき
		if (!is_array($opt)) {
			$opt = array( 'name' => '' );
		}
		
		// 名前とアイコンのパラメータを取得
		$name		=	isset($opt['name']) ? $opt['name'] : '';
		$icon		=	isset($opt['icon']) ? $opt['icon'] : '';
		if ($this->options['flg-debug']) {
			echo '<!-- [Pz-Talk] name=\''.$name.'\' icon=\''.$icon.'\' /-->'.PHP_EOL;
		}
		
		unset($url);
		$i			=	1;
		while (isset($this->options['name-'.$i])) {
			if		($name	==	$this->options['name-'.$i]) {
				if ($this->options['flg-debug']) {
					echo '<!-- [Pz-Talk] name hit i='.$i.' /-->'.PHP_EOL;
				}
				if	($icon	==	'') {
					if ($this->options['flg-debug']) {
						echo '<!-- [Pz-Talk] no icon set URL /-->'.PHP_EOL;
					}
					$url	=	$this->options['url-'.$i];
				}
				$me		=	isset($this->options['me-'.$i]) ? $this->options['me-'.$i] : null;
			}
			if		(($icon) && ($icon == $this->options['name-'.$i])) {
				if ($this->options['flg-debug']) {
					echo '<!-- [Pz-Talk] icon hit i='.$i.' set URL /-->'.PHP_EOL;
				}
				$url		=	$this->options['url-'.$i];
			}
			$i++;
		}
		if (!isset($url)) {
			if ($this->options['flg-debug']) {
				echo '<!-- [Pz-Talk] new face i='.$i.' /-->'.PHP_EOL;
			}
			$url		=	'';
			$me			=	null;
			if ($name) {
				$this->options['name-'.$i]	=	$name;
				$this->options['url-'.$i]	=	$url;
				$this->options['me-'.$i]	=	$me;
				$result = update_option('Pz_Talk_options', $this->options);
			}
		}
		if (!$url) {
			if ($this->options['flg-debug']) {
				echo '<!-- [Pz-Talk] no URL /-->'.PHP_EOL;
			}
			if (!isset($this->options['url-0'])) {
				$this->options['url-0']	=	$this->plugin_dir_url.'img/unknown.png';
				$result = update_option('Pz_Talk_options', $this->options);
			}
			$url	=	$this->options['url-0'];
		}
		
		$opt['name']	=	$name;
		$opt['url']		=	$url;
		$opt['me']		=	$me;
		
		return $opt;
	}

	// スクリプト・CSSの設定
	function enqueue() {
		wp_enqueue_style('pz-talk', plugin_dir_url (__FILE__).'css/style.css');
	}

	// デバッグ情報
	function footer() {
		echo PHP_EOL;
		echo '<!-- [Pz-Talk] Debug information ---'.PHP_EOL;
		echo print_r($this->options, true);
		echo '--- [Pz-Talk] /-->'.PHP_EOL;
	}

	// 管理画面のスクリプト
	function admin_scripts() {
		wp_enqueue_media();		// メディアアップローダの javascript API
		wp_enqueue_script('mediapicker', plugins_url('js/mediapicker.js', __FILE__ ) , array( 'jquery' ), false, true);		// メディアピッカー
		wp_enqueue_script('pz-talk-admin', plugins_url('js/admin.js', __FILE__ ) , array( 'jquery' ), false, true);
		wp_enqueue_style('pz-talk-admin', plugin_dir_url (__FILE__).'css/admin.css');
	}

	// プラグイン一覧のクイックメニュー
	public function action_links($links) {
		$links = array('<a href="options-general.php?page=pz-talk-settings">'.__('Settings', $this->text_domain).'</a>' ) + $links;
		return $links;
	}

	// 管理画面時のスタイルシート、スクリプト設定
	public function admin_footer() {
		if (wp_script_is('quicktags') ) {
			echo '<script>QTags.addButton(\'pz-talk\',\''.__('Talk', $this->text_domain ).'\',\'['.$this->options['code1'].' name=""]\',\'[/'.$this->options['code1'].']\',\'\',\''.__('Make Talk', $this->text_domain ).'\');</script>'.PHP_EOL;
		}
		require_once ('lib/pz-talk-modal.php');

	}

	// 管理画面時のスタイルシート、スクリプト設定
	public function add_mce_button($buttons) {
		if ($this->options['flg-edit-insert']) {
			$buttons[]		=	'pz_talk';
		}
		return	$buttons;
	}

	public function add_mce_plugin($plugins) {
		$plugins['pz_talk']		=	$this->plugin_dir_url .'js/mce.js';
		return	$plugins;
	}

	// 管理画面のサブメニュー追加
	public function add_menu() {
		add_options_page	(__('Talk Settings', $this->text_domain),__('Pz Talk', $this->text_domain), 'manage_options', 'pz-talk-settings', array($this, 'page_settings') );
	}

	// Pz カード 設定画面
	public function page_settings() {
		require_once ('lib/pz-talk-settings.php');
	}

}
$pz_Talk = new Pz_Talk;