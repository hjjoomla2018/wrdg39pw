<?php
/**
 * @version $Id: category.php 99 2017-08-04 10:55:30Z szymon $
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

jimport('joomla.application.component.controllerform');

class DJMediatoolsControllerPresets extends JControllerForm {

	function save($key = NULL, $urlVar = NULL) {
		
		$task = $this->getTask();
		$app = JFactory::getApplication();
		$jform	= $app->input->get('jform', array(), 'array');
		
		$name = $app->input->getCmd('preset');
		
		if($task == 'save2copy') {
			$name = $app->input->getCmd('preset_name');
		}
		
		$name = JFile::makeSafe($name);
		
		if (empty($name)) {
			$this->setMessage(JText::_('COM_DJMEDIATOOLS_PRESET_NAME_EMPTY'), 'error');
			$this->setRedirect('index.php?option=com_djmediatools&view=presets');
			return false;
		}
		
		$file = JPath::clean(JPATH_ROOT . '/media/djmediatools/presets/' . $name . '.json');
		
		if (!is_dir(dirname($file))) {
			JFolder::create(dirname($file));
		}
		
		$params = new JRegistry();
		$params->loadObject($jform);
		
		$data = $params->toString();
		
		if (!@JFile::write($file, $data)) {
			$this->setMessage(JText::sprintf('COM_DJMEDIATOOLS_CAN_NOT_WRITE_TO_FILE', $file), 'error');
			$this->setRedirect('index.php?option=com_djmediatools&view=presets');
			return false;
		} 
		
		$this->setMessage(JText::_('COM_DJMEDIATOOLS_PRESET_SAVED'), 'success');
		$this->setRedirect('index.php?option=com_djmediatools&view=presets&preset='.$name);
		
		return true;
	}
	
	function delete() {
		
		$task = $this->getTask();
		$app = JFactory::getApplication();
		
		$name = $app->input->getCmd('preset');
		$name = JFile::makeSafe($name);
		
		if (empty($name)) {
			$this->setMessage(JText::_('COM_DJMEDIATOOLS_PRESET_NAME_EMPTY'), 'error');
			$this->setRedirect('index.php?option=com_djmediatools&view=presets');
			return false;
		}
		
		$file = JPath::clean(JPATH_ROOT . '/media/djmediatools/presets/' . $name . '.json');
		
		if(!JFile::delete($file)) {
			$this->setMessage(JText::sprintf('COM_DJMEDIATOOLS_CAN_NOT_DELETE_FILE', $file), 'error');
			$this->setRedirect('index.php?option=com_djmediatools&view=presets');
			return false;
		}
		
		$this->setMessage(JText::_('COM_DJMEDIATOOLS_PRESET_DELETED'), 'success');
		$this->setRedirect('index.php?option=com_djmediatools&view=presets');
		
		return true;
	}
}

?>