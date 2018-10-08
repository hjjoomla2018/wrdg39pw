<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
*
*
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
*
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');


class DJClassifiedsControllerProfile extends JControllerLegacy {

	

function ask(){
	$app	= JFactory::getApplication();	
	$uid	= $app->input->getInt('uid');
	$db 	= JFactory::getDBO();
	$user 	= JFactory::getUser();
	$itemid	= $app->input->getInt('Itemid');
	$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
	$session = JFactory::getSession();
	$send_email=0;
	$msg 	= strip_tags(JRequest::getVar('ask_message',''));
	
		$profile_user = JFactory::getUser($uid);
		
		if(!$profile_user->id){
			$msg = JText::_('COM_DJCLASSIFIEDS_WRONG_PROFILE');
			$link = JURI::root();			
			$app->redirect($link,$msg,'error');
		}
				
		//echo '<pre>';print_r($profile_user);die();		
		
		if($par->get('authorname','name')=='name'){
			$u_name = $profile_user->name;
		}else{
			$u_name = $profile_user->username;
		}
		
		$u_slug = $profile_user->id.':'.DJClassifiedsSEO::getAliasName($u_name);
		$link = JRoute::_('index.php?option=com_djclassifieds&view=profile&uid='.$u_slug.DJClassifiedsSEO::getUserProfileItemid(),false);
				
			
	if($par->get('ask_seller_type','0')==0 || $user->id>0){			
			$date_time = JFactory::getDate();
			$date_now  = $date_time->toSQL();
			$date_exp  = mktime();
			
			$date_last5 = date('Y-m-d H:i:s',mktime(date("H"), date("i")-$par->get('ask_limit_one',5), date("s"), date("m"), date("d"),date("Y")));
			$date_lasth = date('Y-m-d H:i:s',mktime(date("H")-1, date("i"), date("s"), date("m"), date("d"),date("Y")));
		
			$query = "SELECT COUNT(id) FROM #__djcf_profiles_msg m "
					."WHERE m.user_from = ".$user->id." AND m.user_to=".$uid." AND m.date>'".$date_last5."'";
						
			$db->setQuery($query);
			$check = $db->loadResult();
			if($check>0){
	    	 	$msg = JText::_('COM_DJCLASSIFIEDS_ASK_MESSAGE_LIMIT');
				$app->redirect($link,$msg);	
			}
		
			$query = "SELECT COUNT(id) FROM #__djcf_profiles_msg m "
					."WHERE m.user_from = ".$user->id." AND m.date>'".$date_lasth."'";	
			$db->setQuery($query);
			$check = $db->loadResult();
	
			if($check>$par->get('ask_limit_hour',15)){
		     	//$link = 'index.php?option=com_djclassifieds&view=item&id='.$id.'&Itemid='.$itemid;
	    	 	$msg = JText::_('COM_DJCLASSIFIEDS_ASK_MESSAGE_LIMIT_HOUR');
				$app->redirect($link,$msg);		
			}
			
			$query = "SELECT COUNT(id) FROM #__djcf_profiles_msg m "
					."WHERE m.user_from = ".$user->id." AND m.date>'".$date_lasth."'";
			$db->setQuery($query);
			$check = $db->loadResult();

			$query ="SELECT f.* FROM #__djcf_fields f "
					."WHERE f.published=1 AND f.source=3 ORDER BY f.name";
			$db->setQuery($query);
			$fields_list =$db->loadObjectList();
			
			$custom_fields_msg='';
			foreach($fields_list as $fl){
				$fl_val = JRequest::getVar($fl->name,'');						
				if(is_array($fl_val)){
					$custom_fields_msg .= $fl->label.": ".implode(', ', $fl_val)."<br />"; 
				}else if($fl_val){
					$custom_fields_msg .= $fl->label.": ".$fl_val."<br />";
				}
			}
			
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$query="INSERT INTO #__djcf_profiles_msg (`user_to`, `user_from`, `ip_address`, `message`,`date`,`custom_fields`)"
			  	." VALUES ( '".$uid."', '".$user->id."','".$user_ip."', '".$db->escape($msg)."','".date("Y-m-d H:i:s")."','".$db->escape($custom_fields_msg)."')"; 
			$db->setQuery($query);
			$db->query();
			$send_email=1;
							
	}else if($par->get('ask_seller_type','0')==0 && $user->id==0){
		$msg = JText::_('COM_DJCLASSIFIEDS_PLEASE_LOGIN');	 
	}else{		
		if($par->get('captcha_type','recaptcha')=='nocaptcha'){
			require_once(JPATH_COMPONENT.DS.'assets'.DS.'nocaptchalib.php');
		}else{
			require_once(JPATH_COMPONENT.DS.'assets'.DS.'recaptchalib.php');
		}
				
		$privatekey = $par->get('captcha_privatekey',"6LfzhgkAAAAAAOJNzAjPz3vXlX-Bw0l-sqDgipgs");
		$is_valid = false;
		
			if($par->get('captcha_type','recaptcha')=='nocaptcha'){
				$response = null;
				$reCaptcha = new ReCaptcha($privatekey);
				if ($_POST["g-recaptcha-response"]) {
					$response = $reCaptcha->verifyResponse(
							$_SERVER["REMOTE_ADDR"],
							$_POST["g-recaptcha-response"]
					);
					if ($response != null && $response->success) {
						$is_valid = true;
					}
				}
			}else{		
			  $resp = recaptcha_check_answer ($privatekey,
	                                  $_SERVER["REMOTE_ADDR"],
	                                  $_POST["recaptcha_challenge_field"],
	                                  $_POST["recaptcha_response_field"]);
			  $is_valid = $resp->is_valid;
			}
			
			if ($is_valid) {
				$user_ip = $_SERVER['REMOTE_ADDR'];
			  
				$date_time = JFactory::getDate();
				$date_now  = $date_time->toSQL();
				$date_exp  = mktime();
				
				$date_last5 = date('Y-m-d H:i:s',mktime(date("H"), date("i")-$par->get('ask_limit_one',5), date("s"), date("m"), date("d"),date("Y")));
				$date_lasth = date('Y-m-d H:i:s',mktime(date("H")-1, date("i"), date("s"), date("m"), date("d"),date("Y")));
			
				$query = "SELECT COUNT(id) FROM #__djcf_profiles_msg m "
						."WHERE m.ip_address = '".$user_ip."' AND m.user_to=".$uid." AND m.date>'".$date_last5."'";
							
				$db->setQuery($query);
				$check = $db->loadResult();
				if($check>0){
			     	//$link = 'index.php?option=com_djclassifieds&view=item&id='.$id.'&Itemid='.$itemid;
		    	 	$msg = JText::_('COM_DJCLASSIFIEDS_ASK_MESSAGE_LIMIT');
					$app->redirect($link,$msg);	
				}
			
				$query = "SELECT COUNT(id) FROM #__djcf_profiles_msg m "
						."WHERE m.ip_address = '".$user_ip."' AND m.date>'".$date_lasth."'";	
				$db->setQuery($query);
				$check = $db->loadResult();
		
				if($check>$par->get('ask_limit_hour',15)){
			     	//$link = 'index.php?option=com_djclassifieds&view=item&id='.$id.'&Itemid='.$itemid;
		    	 	$msg = JText::_('COM_DJCLASSIFIEDS_ASK_MESSAGE_LIMIT_HOUR');
					$app->redirect($link,$msg);		
				}
			
				$user_ip = $_SERVER['REMOTE_ADDR'];
				
				$query ="SELECT f.* FROM #__djcf_fields f "
						."WHERE f.published=1 AND f.source=3 ORDER BY f.name";
				$db->setQuery($query);
				$fields_list =$db->loadObjectList();
					
				$custom_fields_msg='';
				foreach($fields_list as $fl){
					$fl_val = JRequest::getVar($fl->name,'');
					if(is_array($fl_val)){
						$custom_fields_msg .= $fl->label.": ".implode(', ', $fl_val)."<br />";
					}else if($fl_val){
						$custom_fields_msg .= $fl->label.": ".$fl_val."<br />";
					}
				}					
								
				 $query="INSERT INTO #__djcf_profiles_msg (`user_to`, `user_from`, `ip_address`, `message`,`date`,`custom_fields`)"
				 	   ." VALUES ( '".$uid."', '0','".$user_ip."', '".$db->escape($msg)."','".date("Y-m-d H:i:s")."','".$db->escape($custom_fields_msg)."')";
				  	
				$db->setQuery($query);
				$db->query(); 
				$send_email=1;
	
		  }else {
		  	$session->set('askform_name',$_POST['ask_name']);
			$session->set('askform_email',$_POST['ask_email']);
			$session->set('askform_message',$_POST['ask_message']);								
			$message = JText::_("COM_DJCLASSIFIEDS_INVALID_CODE");	
			//$link = 'index.php?option=com_djclassifieds&view=item&cid='.$cid.'&id='.$id.'&ae=1&Itemid='.$itemid.'#ask_form';
			$link = $link.'?ae=1#ask_form';
		  	$app->redirect($link,$message,'error');			
		  }

	}
	
	if($send_email){
		$model_item = $this->getModel('item');
		$profile = $model_item->getProfile($uid);
		$profile['user'] = $profile_user;		
			
			$author= array();
			$author['name'] = JRequest::getString('ask_name','');
			$author['email'] = JRequest::getString('ask_email','');
			$author['user_id'] = '';
			$author['profile'] = '';
				
			if($user->id){
				$author['user_id'] = $user->id;
			
				if($par->get('authorname','name')=='name'){
					$uid_slug = $user->id.':'.DJClassifiedsSEO::getAliasName($user->name);
				}else{
					$uid_slug = $user->id.':'.DJClassifiedsSEO::getAliasName($user->username);
				}
				$profile_itemid = DJClassifiedsSEO::getUserProfileItemid();
			
				$u = JURI::getInstance( JURI::root() );
				if($u->getScheme()){
					$author['profile'] = $u->getScheme().'://';
				}else{
					$author['profile'] = 'http://';
				}
				$author['profile'] .= $u->getHost().JRoute::_('index.php?option=com_djclassifieds&view=profile&uid='.$uid_slug.$profile_itemid);
					
			}
			
			$replyto 	= JRequest::getString('ask_email','');
			$replytoname=JRequest::getString('ask_name','');
			
			DJClassifiedsNotify::messageProfileAskFormContact($profile,$author,$msg,$_FILES,$replyto,$replytoname,$custom_fields_msg);
		
	    	 $msg = JText::_('COM_DJCLASSIFIEDS_MESSAGE_SEND');
	}
	
    $app->redirect($link, $msg);				
}
		
}

?>