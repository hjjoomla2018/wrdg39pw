<?php
/**
 * @version $Id: category.php 104 2017-09-14 18:17:11Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2017 DJ-Extensions.com, All rights reserved.
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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class DJMediatoolsModelPresets extends JModelAdmin
{
	
	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');
		
		JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_djmediatools/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_djmediatools/models/fields');
		
		// Get the form.
		$form = $this->loadForm('com_djmediatools.preset', 'preset', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}
		
		return $form;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_djmediatools.edit.preset.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	public function getItem($pk = null) {
		
		$app = JFactory::getApplication();
		$preset = $pk ? $pk : $app->input->getCmd('preset','');
		$data = array();
		
		if(!empty($preset)) {
			
			$file = JPath::clean(JPATH_ROOT . '/media/djmediatools/presets/' . $preset . '.json');
			
			if(JFile::exists($file)) {
				
				$params = new JRegistry;
				
				$params->loadString(JFile::read($file));
				
				$data = $params->toArray();
			}
		}

		return $data;
	}
	
	public function getItems() {
		
		$path = JPath::clean(JPATH_ROOT.'/media/djmediatools/presets');
		
		if(!JFolder::exists($path)) {
			JFolder::create($path);
		}
		
		$files = JFolder::files($path, '.json');
		
		$options = array();
		
		$options[] = JHtml::_('select.option', '', JText::_('COM_DJMEDIATOOLS_PRESET_CHOOSE'));
		
		if($files) foreach($files as $file) {
			$name = JFile::stripExt($file);
			$options[] = JHtml::_('select.option', $name, ucfirst($name));
		}
		
		return $options;
	}
	
	public function save($data){
		// not used
		
		return false;
	}
	
	public function delete(&$pks){
		// not used
		
		return false;
	}
		
}
