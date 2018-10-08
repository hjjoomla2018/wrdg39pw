<?php
/**
 * @version $Id: review.php 30 2015-02-25 16:01:31Z michal $
 * @package DJ-Reviews
 * @copyright Copyright (C) 2014 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 *
 * DJ-Reviews is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Reviews is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Reviews. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.JPath::clean('/components/com_djreviews/lib/modeladmin.php'));

class DJReviewsModelComment extends DJReviewsModelAdmin
{
	protected $text_prefix = 'COM_DJREVIEWS';
	protected $form_name = 'comment';

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function getTable($type = 'Comments', $prefix = 'DJReviewsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_djreviews.'.$this->form_name, $this->form_name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_djreviews.edit.'.$this->form_name.'.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	protected function _prepareTable(&$table)
	{
		jimport('joomla.filter.output');
	
		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
	}
	
}