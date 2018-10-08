<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.joomla-monster.com/license.html Joomla-Monster Proprietary Use License
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

function modChrome_jmmodule($module, &$params, &$attribs) {
	$moduleTag      = $params->get('module_tag', 'div');
	$headerTag      = htmlspecialchars($params->get('header_tag', 'h3'));
	$bootstrapSize  = (int) $params->get('bootstrap_size', '0');
	$moduleClass    = $bootstrapSize != '0' ? $bootstrapSize : '';
	if($module->showtitle == 0) { $notitle='notitle'; } else $notitle='';
	$title = $module->title;
	$title = preg_split('#\s#', $title);
	$title[0] = '<span>'.$title[0].'</span>';
	$title= implode(' ', $title);

	if (!empty ($module->content)) {
		echo '<'.$moduleTag.' class="jm-module '.htmlspecialchars($params->get('moduleclass_sfx')).'">';
		echo '<'.$moduleTag.' class="jm-module-in">';

		if ((bool) $module->showtitle) {
				echo '<'.$headerTag.' class="jm-title '.$params->get('header_class').'">';
						echo $title;
				echo '</'.$headerTag.'>';
		}

		echo '<'.$moduleTag.' class="jm-module-content clearfix '.$notitle.'">';
		echo $module->content;
		echo '</'.$moduleTag.'>';

		echo '</'.$moduleTag.'>';
		echo '</'.$moduleTag.'>';
	}
}

function modChrome_jmmoduleraw($module, &$params, &$attribs) {
	if ($module->content != '') {
		$moduleTag	  = $params->get('module_tag', 'div');
		echo '<'.$moduleTag.' class="jm-module-raw '.$params->get('moduleclass_sfx').'">';
		echo $module->content;
		echo '</'.$moduleTag.'>';
	}
}
