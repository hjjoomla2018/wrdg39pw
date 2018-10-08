<?php
/**
 * @version $Id: djfolderlist.php 72 2017-08-04 10:30:52Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2017 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MediaTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MediaTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MediaTools. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of folder
 *
 * @since  11.1
 */
class JFormFieldDJMTPreset extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'DJMTPreset';
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		$path = JPath::clean(JPATH_ROOT.'/media/djmediatools/presets');
		
		if(!JFolder::exists($path)) {
			JFolder::create($path);
		}
		
		$files = JFolder::files($path, '.json');
		
		if($files) foreach($files as $file) {
			$name = JFile::stripExt($file);
			$options[] = JHtml::_('select.option', $name, ucfirst($name));
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
