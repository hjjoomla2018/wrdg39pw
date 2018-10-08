<?php
/**
 * @version $Id: reviews.php 35 2015-04-07 13:23:38Z michal $
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

class DJReviewsTableComments extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__djrevs_comments', 'id', $db);
	}
	public function bind($array, $ignore = '')
	{
		$return = parent::bind($array, $ignore);
		if (!$return) {
			return false;
		}
		
		return true;
	}
	
	public function check() {
		
		if (empty($this->review_id)) {
			$this->setError(JText::_('COM_DJREVIEWS_INTERNAL_ERROR_REVIEW_ID'));
			return false;
		}
		
		if (empty($this->item_id)) {
			$this->setError(JText::_('COM_DJREVIEWS_INTERNAL_ERROR_OBJECT_ID'));
			return false;
		}
		
		if (empty($this->item_type)) {
			$this->setError(JText::_('COM_DJREVIEWS_INTERNAL_ERROR_OBJECT_TYPE'));
			return false;
		}
		
		$this->_db->setQuery('SELECT COUNT(*) FROM #__djrevs_objects WHERE id='.(int)$this->item_id);
		if (!$this->_db->loadResult()) {
			$this->setError(JText::_('COM_DJREVIEWS_INTERNAL_ERROR_OBJECT_ID'));
			return false;
		}
		
		$this->_db->setQuery('SELECT COUNT(*) FROM #__djrevs_objects WHERE item_type='.$this->_db->quote($this->item_type));
		if (!$this->_db->loadResult()) {
			$this->setError(JText::_('COM_DJREVIEWS_INTERNAL_ERROR_OBJECT_TYPE'));
			return false;
		}
		
		return parent::check();
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		/**
		 * TODO: it might or might not be temporary, but let's establish min/max rate and set it between <1,5>
		 */
		
		$min = 1;
		$max = 5;

		if (!$this->id) {
			if (!intval($this->created)) {
				$this->created = $date->toSql();
			}
			
			if ($user->id > 0 && !$user->guest) {
				if (empty($this->created_by)) {
					$this->created_by = $user->get('id');
				}
			}
			
			if (empty($this->ip)) {
				$client  = @$_SERVER['HTTP_CLIENT_IP'];
			    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			    $remote  = $_SERVER['REMOTE_ADDR'];
			
			    if(filter_var($client, FILTER_VALIDATE_IP)) {
			        $this->ip = $client;
			    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
			        $this->ip = $forward;
			    } else {
			        $this->ip = $remote;
			    }
			}
			/*
			if (empty($this->post_info)) {
				$this->post_info = json_encode($_SERVER);
			}
			 * 
			 */
		}

		$actual_user = $user;
		
		if ($this->created_by > 0 && $this->created_by != $user->id) {
			$actual_user = JFactory::getUser($this->created_by);
		}
		
		if ($actual_user) {
			if (empty($this->user_name)) {
				$this->user_name = $actual_user->name;
			}
			if (empty($this->user_login)) {
				$this->user_login = $actual_user->username;
			}
			if (empty($this->email)) {
				$this->email = $actual_user->email;
			}
		}
		
		$is_stored = parent::store($updateNulls);
		
		if (!$is_stored) {
			return false;
		}
		
		return true;
	}
}