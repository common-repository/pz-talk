<?php if (!function_exists("get_option")) die; ?>
<script type="text/javascript">
	function swap(a,b) {
		var s;
		s = document.getElementById("name-" + a).value;
 		document.getElementById("name-" + a).value = document.getElementById("name-" + b).value;
 		document.getElementById("name-" + b).value = s;
		s = document.getElementById("me-" + a).checked;
 		document.getElementById("me-" + a).checked = document.getElementById("me-" + b).checked;
 		document.getElementById("me-" + b).checked = s;
		s = document.getElementById("url-" + a).value;
 		document.getElementById("url-" + a).value = document.getElementById("url-" + b).value;
 		document.getElementById("url-" + b).value = s;
		s = document.getElementById("img-" + a).src;
 		document.getElementById("img-" + a).src = document.getElementById("img-" + b).src;
 		document.getElementById("img-" + b).src = s;
	}
</script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e('Talk Settings', $this->text_domain); ?></h2>
	<div id="settings" style="clear:both;">
<?php
		if ( isset($_POST['properties'])) {
			check_admin_referer('pz_options');
			$this->options = $_POST['properties'];
			
			// セットされていないオプション項目をnullでセットする
			foreach ($this->defaults as $key => $value) {
				if (!isset($this->options[$key])) {
					$this->options[$key]	=	null;
				}
			}

			$i			=	1;
			while (isset($this->options['name-'.$i])) {
				if ($this->options['name-'.$i] == '')  {
					$j = $i + 1;
					while (isset($this->options['name-'.$j])) {
						if ($this->options['name-'.$j]) {
							$this->options['name-'.$i]	=	$this->options['name-'.$j];
							$this->options['me-'.$i]	=	$this->options['me-'.$j];
							$this->options['url-'.$i]	=	$this->options['url-'.$j];
							$this->options['name-'.$j]	=	'';
							$this->options['me-'.$j]	=	'';
							$this->options['url-'.$j]	=	'';
							break;
						}
						$j++;
					}
				}
				$i++;
			}
			
			$i			=	1;
			while (isset($this->options['name-'.$i])) {
				if ($this->options['name-'.$i] == '')  {
					unset($this->options['name-'.$i]);
					unset($this->options['me-'.$i]);
					unset($this->options['url-'.$i]);
				}
				$i++;
			}
			
			if (isset($this->options['initialize']) && $this->options['initialize'] == '1') {
				delete_option('Pz_Talk_options');
				$this->options	=	$this->defaults;
			}
			
			if (!$this->options['url-0']) {
				$this->options['url-0']		=	$this->plugin_dir_url.'img/unknown.png';
			}
			
			$result = true;
			if ($this->options['code1'] == '') {
				echo '<div class="error fade"><p><strong>'.__('Short code is not set.', $this->text_domain).'</strong></p></div>';
				$result = false;
			}
			
			$this->options['message-me-right']	=	'1';
			
			// サムネイルのキャッシュディレクトリの用意
			$wp_upload_dir			=	wp_upload_dir();
			$icon_dir			=	$wp_upload_dir['basedir'].'/'.$this->slug.'/icon/';
			$icon_url			=	$wp_upload_dir['baseurl'].'/'.$this->slug.'/icon/';
			if		(!is_dir($icon_dir)) {
				$icon_dir		=	$this->plugin_dir_path.'icon/';
				$icon_url		=	$this->plugin_dir_url .'icon/';
				if	(!is_dir($icon_dir)) {
					$icon_dir	=	null;
					$icon_url	=	null;
				}
			}
			$this->options['icon-dir']	= $icon_dir;
			$this->options['icon-url']	= $icon_url;
			
			// オプションの更新
			if ($result == true) {
				$result = update_option('Pz_Talk_options', $this->options);
				if ($result == true) {
					echo '<div class="updated fade"><p><strong>'.__('Changes saved.', $this->text_domain).'</strong></p></div>';
				} else {
					echo '<div class="error fade"><p><strong>'.__('Not changed.', $this->text_domain).'</strong></p></div>';
				}
			}
		}
		?>
		<form action="" method="post">
			<?php wp_nonce_field('pz_options'); ?>

			<h3><?php _e('How to', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e("Plugin page", $this->text_domain); ?></th>
					<td><A href="https://popozure.info/pz-talk" target="_blank">https://popozure.info/pz-talk</A></td>
				</tr>
			</table>

			<h3><?php _e('Persons', $this->text_domain); ?></h3>
			<table class="form-table widefat striped">
				<tr valign="top">
					<th scope="column" style="width:2em; text-align:center;"></th>
					<th scope="column" style="width:8em; text-align:center;"><?php _e('Name', $this->text_domain); ?></th>
					<th scope="column" style="width:2em; text-align:center;"><?php _e('Me', $this->text_domain); ?></th>
					<th scope="column" style="text-align:center;"><?php _e('Image URL', $this->text_domain); ?></th>
					<th scope="column" style="text-align:center; width:64px;"><?php _e('Image', $this->text_domain); ?></th>
					<th scope="column" style="text-align:center; width:32px;"></th>
				</tr>
				<tr valign="top" style="height: 98px;">
					<td></td>
					<td><?php _e('(unknown)', $this->text_domain); ?></td>
					<td></td>
					<td><input name="properties[url-0]" type="text" id="url-0" value="<?php echo esc_attr($this->options['url-0']) ?>" class="regular-text" style="width: 40em;" /><input type="button" id="mediaselect-0" name="mediaselect" value="<?php _e('Select', $this->text_domain) ?>..." /></td>
					<td><img src="<?php echo $this->options['url-0']; ?>" alt="" style="max-width:64px; max-height:64px;"></td>
					<td></td>
				</tr>
		<?php
		$i			=	0;
		do {
			$i++;
?>				<tr valign="top" style="height:98px;">
					<td><?php echo $i; ?></td>
					<td><input name="properties[name-<?php echo $i; ?>]" type="text" id="name-<?php echo $i; ?>" value="<?php echo esc_attr(isset($this->options['name-'.$i]) ? $this->options['name-'.$i] : ''); ?>" class="regular-text" style="width: 8em;" /></td>
					<td><input name="properties[me-<?php echo $i; ?>]" type="checkbox" id="me-<?php echo $i; ?>" value="1" <?php echo (isset($this->options['me-'.$i]) ? 'checked="checked"' : '') ?> /></td>
					<td><input name="properties[url-<?php echo $i; ?>]" type="text" id="url-<?php echo $i; ?>" value="<?php echo esc_attr(isset($this->options['url-'.$i]) ? $this->options['url-'.$i] : ''); ?>" class="regular-text" style="width: 40em;" onInput="document.getElementById('img-<?php echo $i; ?>').src = this.value;" /><input type="button" id="mediaselect-<?php echo $i; ?>" name="mediaselect" value="<?php _e('Select', $this->text_domain); ?>..." /></td>
					<td><img src="<?php echo (isset($this->options['url-'.$i]) ? $this->options['url-'.$i] : ''); ?>" alt="" id="img-<?php echo $i; ?>" style="max-width:64px; max-height:64px;"></td>
					<td><input type="button" id="up-<?php echo $i; ?>" name="up" value="<?php _e('↑', $this->text_domain); ?>" onClick="swap(<?php echo ($i - 1).','.$i; ?>);" <?php if ($i == 1) echo 'disabled=disabled '?>/><br><input type="button" id="up-<?php echo $i; ?>" name="up" value="<?php _e('↓', $this->text_domain); ?>" onClick="swap(<?php echo $i.','.($i + 1); ?>);" <?php if (!isset($this->options['name-'.$i])) echo 'disabled=disabled '?>/></td>
				</tr>
<?php	} while (isset($this->options['name-'.$i]));
		?>
			</table>
			<?php submit_button(); ?>

			<h3><?php _e('Editor', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Add insert button', $this->text_domain); ?></th>
					<td><label><input name="properties[flg-edit-insert]" type="checkbox" id="check" value="1" <?php checked(isset($this->options['flg-edit-insert']) ? $this->options['flg-edit-insert'] : null, 1); ?> /><?php _e('Add insert button to visual editor.', $this->text_domain); ?></label></td>
				</tr>
			</table>
			<?php submit_button(); ?>

			<h3><?php _e('Shortcode', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Talk', $this->text_domain); ?></th>
					<td>[<input name="properties[code1]" type="text" id="code1" value="<?php echo esc_attr($this->options['code1']); ?>" class="regular-text" style="width: 8em;" onKeyUp="document.getElementById('close1').innerText = document.getElementById('code1').value;" /> name="<span style="color: #aabbff;"><?php _e('Name', $this->text_domain); ?></span>"]<span style="color: #bbaaff;"><?php _e('Hello, world!!', $this->text_domain); ?></span>[/<span id="close1"><?php echo esc_attr($this->options['code1']); ?></span>]
						<p><?php _e('Case-sensitive', $this->text_domain); ?></p></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Messenger', $this->text_domain); ?></th>
					<td>[<input name="properties[code2]" type="text" id="code2" value="<?php echo esc_attr($this->options['code2']); ?>" class="regular-text" style="width: 8em;" onKeyUp="document.getElementById('close2').innerText = document.getElementById('code2').value;" /> name="<span style="color: #aabbff;"><?php _e('Name', $this->text_domain); ?></span>"]<span style="color: #bbaaff;"><?php _e('Hello, world!!', $this->text_domain); ?></span>[/<span id="close2"><?php echo esc_attr($this->options['code2']); ?></span>]
						<p><?php _e('Case-sensitive', $this->text_domain); ?></p></td>
				</tr>
			</table>
			<?php submit_button(); ?>

			<h3><?php _e('Right justify', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Talk', $this->text_domain); ?></th>
					<td><input name="properties[talk-me-right]" type="checkbox" id="check" value="1" <?php checked(isset($this->options['talk-me-right']) ? $this->options['talk-me-right'] : null, 1); ?> /><?php _e('Right justify if it is named "Me".', $this->text_domain); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Messenger', $this->text_domain); ?></th>
					<td><input name="properties[message-me-right]" type="checkbox" id="check" value="1" <?php checked(isset($this->options['message-me-right']) ? $this->options['message-me-right'] : null, 1); ?> checked="checked" disabled="disabled" /><?php _e('Right justify if it is named "Me".', $this->text_domain); ?></td>
				</tr>
			</table>
			<?php submit_button(); ?>

			<h3><?php _e('Debug', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Debug mode', $this->text_domain); ?></th>
					<td><label><input name="properties[flg-debug]" type="checkbox" id="check" value="1" <?php checked(isset($this->options['flg-debug']) ? $this->options['flg-debug'] : null, 1); ?> /><?php _e('The information required for investigation is output to the source.', $this->text_domain); ?></label></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Return to the initial setting', $this->text_domain); ?></th>
					<td><label><input name="properties[initialize]" type="checkbox" id="check" value="1" <?php checked(isset($this->options['initialize']) ? $this->options['initialize'] : null, 1); ?> /><?php _e('* Usually not used.', $this->text_domain); ?></label></td>
				</tr>
			</table>
			<?php submit_button(); ?>

			<h3><?php _e('etc', $this->text_domain); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e("Author's site", $this->text_domain); ?></th>
					<td><?php echo __('Popozure.', $this->text_domain).' ('.__("Poporon's PC daily diary", $this->text_domain).')'; ?><BR><A href="https://popozure.info" target="_blank">https://popozure.info</A></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e("Author's Twitter account", $this->text_domain); ?></th>
					<td><A href="https://twitter.com/popozure" target="_blank">https://twitter.com/popozure</A></td>
				</tr>
				<tr valign="top" style="display: none;">
					<th scope="row"><?php _e("Donation", $this->text_domain); ?></th>
					<td>https://www.amazon.co.jp/gp/registry/wishlist/2KIBQLC1VLA9X</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
		function pz_TrimNum($val, $zero = 0 ) {
			$val		=	preg_replace('/[^0-9]/', '', $val) - 0;
			if ($val	==	0) {
				$val	=	$zero;
				$val	=	preg_replace('/[^0-9]/', '', $val) - 0;
			}
			return	$val;
		}
