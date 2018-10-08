<?php
/**
 * @version $Id: reviewform.php 39 2015-04-30 09:28:14Z michal $
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

require_once JPath::clean(JPATH_ADMINISTRATOR.'/components/com_djreviews/models/comment.php');

jimport('joomla.application.component.helper');

class DJReviewsModelCommentForm extends DJReviewsModelComment
{
	public $object = null;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		JForm::addFormPath(__DIR__.JPath::clean('/forms'));
	}
	
	protected function populateState()
	{
		parent::populateState();
	
		$app = JFactory::getApplication();
	
		$review_id = $app->input->getInt('review_id');
		$this->setState('comment.review_id', $review_id);
		
		$item_id = $app->input->getInt('item_id');
		$this->setState('comment.item_id', $item_id);
		if($item_id > 0){
			$db = JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__djrevs_objects WHERE id=".$item_id );
			$object = $db->loadObject();
			if($object){
				$this->setState('comment.item_type', $object->item_type);
			}
		}
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
		
		$review_id = $this->getState('comment.review_id');
		if ($review_id) {
			$form->setValue('review_id', null, $review_id);
		}
		
		$item_id = $this->getState('comment.item_id');
		if ($item_id) {
			$form->setValue('item_id', null, $item_id);
		}
		
		$item_type = $this->getState('comment.item_type');
		if ($item_type) {
			$form->setValue('item_type', null, $item_type);
		}
		
		$user = JFactory::getUser();
		if ($user->id > 0) {
			$form->setValue('created_by', null, $user->id);
			$form->setValue('user_name', null, $user->name);
			$form->setValue('user_login', null, $user->username);
			$form->setValue('email', null, $user->email);
		}
		
		$params = DJReviewsHelper::getParams();
		
		$requireTitle = (int)$params->get('title', 2);
		$requireMessage = (int)$params->get('message', 2);
		if ($requireTitle == 2) {
			$form->setFieldAttribute('title', 'required', 'true');
			$form->setFieldAttribute('title', 'class', $form->getFieldAttribute('title', 'class').' required');
		} else if ($requireTitle == 0) {
			$form->removeField('title');
		}
		if ($requireMessage == 2) {
			$form->setFieldAttribute('message', 'required', 'true');
			$form->setFieldAttribute('message', 'class', $form->getFieldAttribute('message', 'class').' required');
		} else if ($requireMessage == 0) {
			$form->removeField('message');
		}
		
		return $form;
	}
	
	public function validate($form, $data, $group = null) {
		$user = JFactory::getUser();
		$params  = JComponentHelper::getParams('com_djreviews');
		$db = JFactory::getDbo();
		
		if (isset($data['id']) && $data['id'] > 0) {
			// if the comment has been edited we don't change it's state
			unset($data['published']);
			
			if ($item = $this->getItem($data['id'])) {
				$data['created_by'] = $item->created_by;
				$data['user_name'] = $item->user_name;
				$data['user_login'] = $item->user_login;
				$data['email'] = $item->email;
			}
			
		} else {
			if ($user->authorise('comment.autopublish', 'com_djreviews') || $user->authorise('core.edit.state', 'com_djreviews')){
				$data['published'] = 1;
			} else {
				$data['published'] = 0;
			}
			
			if ($user->id > 0) {
				$data['created_by'] = $user->id;
				$data['user_name'] = $user->name;
				$data['user_login'] = $user->username;
				$data['email'] = $user->email;
			}
		}
		
		$title_splited = explode(' ', $data['title']);
		$msg_splited = explode(' ', $data['message']);
		if($params->get('word_blacklist',"")){
			$word_blacklist = explode(',', $params->get('word_blacklist',""));
			$word_whitelist = explode(',', $params->get('word_whitelist',""));
			
			foreach($msg_splited as &$word){
					foreach($word_blacklist as $bl){
						$bl = trim(JString::strtolower($bl));
						if (!$bl) {
							continue;
						}
						$tmpWord = JString::strtolower($word);
						if (strstr($tmpWord, $bl)){
							$bl_found =1;
							foreach($word_whitelist as $wl){
								$wl = trim(JString::strtolower($wl));
								if ($wl && strstr($tmpWord, $wl)){
									$bl_found = 0;
									break;
								}
							}
							if ($bl_found){
								$stars_l = JString::strlen($word);
								$stars = '';
								for($i=0; $i < JString::strlen($bl); $i++){
									$stars .= '*';
								} 
								$word = JString::str_ireplace($bl, $stars, $word);
							}
						}
					}
			}
			unset($word);
			
			foreach($title_splited as &$word){
				foreach($word_blacklist as $bl){
					$bl = trim(JString::strtolower($bl));
					if (!$bl) {
						continue;
					}
					$tmpWord = JString::strtolower($word);
					if (strstr($tmpWord, $bl)){
						$bl_found =1;
						foreach($word_whitelist as $wl){
							$wl = trim(JString::strtolower($wl));
							if ($wl && strstr($tmpWord, $wl)){
								$bl_found = 0;
								break;
							}
						}
						if ($bl_found){
							$stars_l = JString::strlen($word);
							$stars = '';
							for($i=0; $i < JString::strlen($bl); $i++){
								$stars .= '*';
							}
							$word = JString::str_ireplace($bl, $stars, $word);
						}
					}
				}
			}
			unset($word);
			
			$data['message'] = implode(' ', $msg_splited);
			$data['title'] = implode(' ', $title_splited);
		}

		return parent::validate($form, $data, $group);
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
	}
	
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		return (bool)($user->authorise('core.delete', 'com_djreviews') 
				|| ($user->authorise('comment.delete.own', 'com_djreviews') && $user->id == $record->created_by));
	}
	
	public function getObject($id = false){
		if (empty($this->object)) {
			$db  = JFactory::getDbo();
			$app = JFactory::getApplication();
			$item_id = 0;
			if ($id !== false) {
				$item_id = $id;
			} else {
				$item_id = $this->getState('comment.item_id');
				
				if(!$item_id && $app->input->getInt('item_id') > 0){
					$item_id = $app->input->getInt('item_id');
				}
				
				if (!$item_id && $this->getState('commentform.id') > 0) {
					if ($item = $this->getItem()) {
						$item_id = $item->item_id;
					}
				}
			}
		
			if ($item_id) {
				$query = $db->getQuery(true);
				$query->select('ro.* ');
				$query->from('#__djrevs_objects ro');
				$query->where('ro.id='.(int)$item_id);
				$db->setQuery($query);
				$this->object = $db->loadObject();
			}
		}
		
		return $this->object;
	}
	
}