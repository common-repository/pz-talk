<?php if (!function_exists("get_option")) die;
		echo '<div id="pz-talk-open"></div>'.PHP_EOL;
		echo '<div id="pz-talk-modal">'.PHP_EOL;
		echo '  <div id="pz-talk-close"><a>×</a></div>'.PHP_EOL;
		echo '  <div id="pz-talk-content">'.PHP_EOL;
		echo '    <form action="" method="post">'.PHP_EOL;
		echo '      <label>'.__('Type', $this->text_domain).'</label>'.PHP_EOL;
		echo '      <select id="pz-talk-code">'.PHP_EOL;
		echo '        <option value="'.$this->options['code1'].'">'.__('Talk', $this->slug ).'</option>'.PHP_EOL;
		echo '        <option value="'.$this->options['code2'].'">'.__('Messenger', $this->slug ).'</option>'.PHP_EOL;
		echo '      </select>'.PHP_EOL;
		echo '      &nbsp;<label>'.__('Name', $this->text_domain).'</label>'.PHP_EOL;
		echo '      <select id="pz-talk-name">'.PHP_EOL;
		$i = 1;
		while (isset($this->options['name-'.$i])) {
			echo '        <option value="'.$this->options['name-'.$i].'">'.$this->options['name-'.$i].'</option>'.PHP_EOL;
			$i++;
		}
		echo '      </select>'.PHP_EOL;
		echo '      &nbsp;<label>'.__('Icon', $this->text_domain).'</label>'.PHP_EOL;
		echo '      <select id="pz-talk-icon">'.PHP_EOL;
		$i = 1;
		while (isset($this->options['name-'.$i])) {
			echo '        <option value="'.$this->options['name-'.$i].'">'.$this->options['name-'.$i].'</option>'.PHP_EOL;
			$i++;
		}
		echo '      </select>'.PHP_EOL;
		echo '      &nbsp;<label>'.__('Serif', $this->text_domain).'</label><input id="pz-talk-serif" type="text" size="30">'.PHP_EOL;
		echo '      <input id="pz-talk-insert" type="submit" value="'.__('Insert', $this->slug ).'" onClick="return false;" >'.PHP_EOL;
		echo '    </form>'.PHP_EOL;
		echo '  </div>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<div id="pz-talk-overlay"></div>'.PHP_EOL;