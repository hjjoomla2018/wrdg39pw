<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

// No direct access.
defined('_JEXEC') or die;
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djclassifieds'.DS.'tables');
jimport('joomla.application.component.controlleradmin');

class DJClassifiedsControllerProfiles extends JControllerAdmin
{
	public function getModel($name = 'Profile', $prefix = 'DJClassifiedsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	
	function recreateAvatarts(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_RECREATING_AVATARS'), 'generic.png');
		 
		$cid = JRequest::getVar( 'cid', array(), 'default', 'array' );
		JArrayHelper::toInteger($cid);
	
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_DJCLASSIFIEDS_SELECT_ITEM_TO_RECREATE_AVATARS' ) );
		}
		
		$profile_watermark = 0;
		if($par->get('watermark',0)==1){
			$profile_watermark = 1;
		}
	
		$tmp = array();
		$tmp[0] = $cid[0];
		unset($cid[0]);
		$db =  JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_images WHERE item_id =  ".$tmp[0] ." AND type='profile' ";
		$db->setQuery($query);
		$images = $db->loadObjectList();
		if($images){
			$nw = $par->get('profth_width',120);
			$nh = $par->get('profth_height',120);
			$nws = $par->get('prof_smallth_width',50);
			$nhs = $par->get('prof_smallth_height',50);
		
			foreach($images as $image){
				$path = JPATH_SITE.$image->path.$image->name;
				if (JFile::exists($path.'.'.$image->ext)){
					if (JFile::exists($path.'_th.'.$image->ext)){
						JFile::delete($path.'_th.'.$image->ext);
					}					
					if (JFile::exists($path.'_ths.'.$image->ext)){
						JFile::delete($path.'_ths.'.$image->ext);
					}
	
					//DJClassifiedsImage::makeThumb($path.$images[$ii], $nws, $nhs, 'ths');
					DJClassifiedsImage::makeThumb($path.'.'.$image->ext,$path.'_th.'.$image->ext, $nw, $nh, false, true, $profile_watermark);
					DJClassifiedsImage::makeThumb($path.'.'.$image->ext,$path.'_ths.'.$image->ext, $nws, $nhs, false, true, $profile_watermark);					
				}
			}
		}
	
		 
		if (count( $cid ) < 1) {
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=profiles', JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED') );
		} else {
			$cids = null;
			foreach ($cid as $value) {
				$cids .= '&cid[]='.$value;
			}
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_RESIZING_ITEM').' [id = '.$tmp[0].']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=profiles.recreateAvatarts'.$cids);
		}
		//$redirect = 'index.php?option=com_djclassifieds&view=items';
		//$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}
	

	function cleardata()
	{
		JPluginHelper::importPlugin('djclassifieds');
		$app  = JFactory::getApplication();
		$cid  = JRequest::getVar('cid', array (), '', 'array');
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		 
		 
		if (!$user->authorise('core.delete', 'com_djclassifieds')) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=profiles' );
			return false;
		}
		 
		if (count($cid))
		{
			//$cids = implode(',', $cid);
			
			foreach($cid as $uid){
				$query = "SELECT COUNT(id) FROM #__djcf_items WHERE user_id = ".$uid."  ";
				$db->setQuery($query);
				$u_items = $db->loadResult();
				if($u_items>0){
					$app->redirect('index.php?option=com_djclassifieds&view=profiles', JText::_('COM_DJCLASSIFIEDS_DELETE_ITEMS_BEFORE_CLEARING_PROFILE'),'error');
				}																												
			
				$query = "SELECT p.* FROM #__djcf_profiles p WHERE p.user_id = ".$uid."  ";
				$db->setQuery($query);
				$profile = $db->loadObject();								
				
				$p_user = JFactory::getUser($uid);				
				
				$dispatcher->trigger('onBeforeDJClassifiedsProfileAdvert', array($uid, $profile));				
					
				$query = "SELECT * FROM #__djcf_images WHERE item_id = ".$uid." AND type='profile' ";
				$db->setQuery($query);
				$user_images =$db->loadObjectList('id');				
					
				if($user_images){
					foreach($user_images as $user_img){
						$path_to_delete = JPATH_ROOT.$user_img->path.$user_img->name;
						if (JFile::exists($path_to_delete.'.'.$user_img->ext)){
							JFile::delete($path_to_delete.'.'.$user_img->ext);
						}						
						if (JFile::exists($path_to_delete.'_th.'.$user_img->ext)){
							JFile::delete($path_to_delete.'_th.'.$user_img->ext);
						}
						if (JFile::exists($path_to_delete.'_ths.'.$user_img->ext)){
							JFile::delete($path_to_delete.'_ths.'.$user_img->ext);
						}
					}
				}
									
					
				$query = "DELETE FROM #__djcf_fields_values_profile WHERE user_id = ".$uid." ";
				$db->setQuery($query);
				$db->query();
				
				$query = "DELETE FROM #__djcf_itemsask WHERE user_id = ".$uid." ";
				$db->setQuery($query);
				$db->query();
				 
				$query = "DELETE FROM #__djcf_images WHERE item_id = ".$uid." AND type='profile' ";
				$db->setQuery($query);
				$db->query();
				
				$query = "DELETE FROM #__djcf_profiles WHERE user_id = ".$uid." ";
				$db->setQuery($query);
				$db->query();
				
				$app->enqueueMessage(JText::_('COM_DJCLASSIFIEDS_PROFILE_CLEARED_FOR_USER').' : '.$p_user->name);
				
			}
			 
		}
		$app->redirect('index.php?option=com_djclassifieds&view=profiles');
	}
	
	public function exportUserData(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid', array (), '', 'array');		
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$uid  = $ids[0];
		
		$profile = array();
		$profile['id'] = $uid;
		
		$profileXML = new SimpleXMLElement("<profile></profile>");
		$profileXML->addChild('uid',$uid);		
				
		$juser = JFactory::getUser($uid); 
		$profile['name'] = $juser->name;
		$profile['username'] = $juser->username;
		$profile['email'] = $juser->email;
		
		$profileXML->addChild('name',$profile['name']);
		$profileXML->addChild('username',$profile['username']);
		$profileXML->addChild('email',$profile['email']);
		
		$query ="SELECT * FROM #__djcf_images WHERE item_id = ".$uid." AND type='profile' LIMIT 1 ";
		$db->setQuery($query);
		$profile['img']=$db->loadObject();
		
		$profile_img = '';
		if($profile['img']){
			$profile_img = JURI::root();
			if(substr($profile['img']->path, 0,1)=='/'){
				$profile_img .= substr($profile['img']->path, 1);
			}
			$profile_img .= $profile['img']->name.'_th.'.$profile['img']->ext;
		}
		
		$profileXML->addChild('img',$profile_img);
		
				
		$query ="SELECT f.*, v.value, v.value_date, v.value_date_to FROM #__djcf_fields f "
				."LEFT JOIN (SELECT * FROM #__djcf_fields_values_profile WHERE user_id=".$uid.") v "
				."ON v.field_id=f.id "
				."WHERE f.published=1 AND f.source=2 AND f.access=0 ORDER BY f.ordering";
		$db->setQuery($query);
		$profile['data']= $db->loadObjectList();
		
		$profile_fields = $profileXML->addChild('details');
		foreach($profile['data'] as $field){
			$field_xml = $profile_fields->addChild($field->name, $field->value);
			$field_xml->addAttribute('label',$field->label);
		}
		

		$query ="SELECT * FROM #__djcf_profiles WHERE user_id = ".$uid." LIMIT 1 ";
		$db->setQuery($query);
		$profile['details']=$db->loadObject();
		
		$profile_location = $profileXML->addChild('location');		
		
		if(isset($profile['details']->user_id)){
			if($profile['details']->region_id){
				$reg_path = DJClassifiedsRegion::getParentPath($profile['details']->region_id);
				$reg_path = array_reverse($reg_path);
				
				for($ri=0;$ri<5;$ri++){
					$rii= $ri+1;
					if(isset($reg_path[$ri])){
						$region = $profile_location->addChild('region'.$rii, $reg_path[$ri]->name);
						$region->addAttribute(id,$reg_path[$ri]->id);
						$region->addAttribute(country,$reg_path[$ri]->country);
						$region->addAttribute(city,$reg_path[$ri]->city);
					}else{
						$profile_location->addChild('region'.$rii);
					}
				}
				
			}else{
				for($ri=1;$ri<6;$ri++){
					$profile_location->addChild('region'.$ri);
				}
			}			
			$profile_location->addChild('address',$profile['details']->address);
			$profile_location->addChild('post_code',$profile['details']->post_code);
			$profile_location->addChild('latitude',$profile['details']->latitude);
			$profile_location->addChild('longitude',$profile['details']->longitude);
		}else{
			for($ri=1;$ri<6;$ri++){
				$profile_location->addChild('region'.$ri);
			}
			$profile_location->addChild('address');
			$profile_location->addChild('post_code');
			$profile_location->addChild('latitude');
			$profile_location->addChild('longitude');
		}
		//echo '<pre>';print_r($profile['details']);die();

		
		$query = "SELECT p.*, i.name as i_name,pp.name as pp_name, plan.name as plan_name, u.name as u_name, o.u_order_id, o.u_order_name, o.u_order_email,i_order_name,i_order_id FROM #__djcf_payments p "
				."LEFT JOIN #__djcf_items i ON i.id=p.item_id "
				."LEFT JOIN #__djcf_points pp ON pp.id=p.item_id "
				."LEFT JOIN #__djcf_plans plan ON plan.id=p.item_id "
				."LEFT JOIN (SELECT o.item_id, o.id, i.user_id as u_order_id,i.name as i_order_name,o.item_id as i_order_id, u.name as u_order_name, u.email as u_order_email
							FROM #__djcf_orders o, #__djcf_items i, #__users u WHERE o.item_id= i.id AND i.user_id=u.id ) o ON o.id=p.item_id "
				."LEFT JOIN #__users u ON u.id=p.user_id "
				." WHERE p.user_id=".$uid." ORDER BY id ASC ";
	
		$db->setQuery($query);
		$profile['payments']=$db->loadObjectList();		
		$payments = $profileXML->addChild('payments');
		if(count($profile['payments'])){
			foreach($profile['payments'] as $pay){
				$payment = $payments->addChild('payment');
				$payment->addChild('id',$pay->id);
				$payment->addChild('item_id',$pay->item_id);
				
				if($pay->type==5){
					$payment->addChild('item_name',htmlentities($pay->i_name));
				}else if($pay->type==4){
					$payment->addChild('item_name',htmlentities($pay->i_name));
				}else if($pay->type==3){
					$payment->addChild('item_name',htmlentities($pay->plan_name)); 
				}else if($pay->type==2){
					$payment->addChild('item_name',htmlentities($pay->i_name));
				}else if($pay->type==1){
					$payment->addChild('item_name',htmlentities($pay->pp_name));
				}else{
					$payment->addChild('item_name',htmlentities($pay->i_name));
				}
								
				if($pay->type==5){
					$payment->addChild('type',JText::_('COM_DJCLASSIFIEDS_OFFER'));
				}else if($pay->type==4){
					$payment->addChild('type', JText::_('COM_DJCLASSIFIEDS_BUYNOW'));
				}else if($pay->type==3){
					$payment->addChild('type', JText::_('COM_DJCLASSIFIEDS_SUBSCRIPTION_PLAN'));
				}else if($pay->type==2){
					$payment->addChild('type',JText::_('COM_DJCLASSIFIEDS_PROMOTION_MOVE_TO_TOP'));
				}else if($pay->type==1){
					$payment->addChild('type',JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGE'));
				}else{
					$payment->addChild('type', JText::_('COM_DJCLASSIFIEDS_ADVERT'));
				}
				

				$payment->addChild('price',$pay->price);
				$payment->addChild('status',$pay->status);
				$payment->addChild('date',$pay->date);
				$payment->addChild('method',$pay->method);
				$payment->addChild('ip_address',$pay->ip_address);
																
			}
		}
		
		//echo '<pre>';print_r($profile['payments']);die();
		
		$query ="SELECT o.* , i.name as i_name FROM #__djcf_offers o "
				."LEFT JOIN #__djcf_items i ON i.id=o.item_id "
				."WHERE o.user_id = ".$uid." ";
		$db->setQuery($query);
		$profile['offers']=$db->loadObjectList();
		$offers_xml = $profileXML->addChild('offers');
		if(count($profile['offers'])){
			foreach($profile['offers'] as $offer){
				$offer_xml = $offers_xml->addChild('offer');
				$offer_xml->addChild('id',$offer->id);
				$offer_xml->addChild('item_id',$offer->item_id);
				$offer_xml->addChild('item_name',$offer->i_name);
				$offer_xml->addChild('quantity',$offer->quantity);
				$offer_xml->addChild('price',$offer->price);
				$offer_xml->addChild('currency',$offer->currency);
				$offer_xml->addChild('date',$offer->date);
				$offer_xml->addChild('ip_address',$offer->ip_address);
				$offer_xml->addChild('message',$offer->message);
			}
		}
		
		
		$query ="SELECT o.* , i.name as i_name FROM #__djcf_orders o "
				."LEFT JOIN #__djcf_items i ON i.id=o.item_id "
				."WHERE o.user_id = ".$uid." ";
		$db->setQuery($query);
		$profile['orders']=$db->loadObjectList();
		
		//echo '<pre>';print_r($profile['orders']);die();
		
		$orders_xml = $profileXML->addChild('orders');
		if(count($profile['orders'])){
			foreach($profile['orders'] as $order){
				$order_xml = $orders_xml->addChild('offer');
				$order_xml->addChild('id',$order->id);
				$order_xml->addChild('item_id',$order->item_id);
				$order_xml->addChild('item_name',$order->i_name);
				$order_xml->addChild('quantity',$order->quantity);
				$order_xml->addChild('price',$order->price);
				$order_xml->addChild('currency',$order->currency);
				$order_xml->addChild('date',$order->date);
				$order_xml->addChild('ip_address',$order->ip_address);
			}
		}
		
		$query ="SELECT a.* , i.name as i_name FROM #__djcf_itemsask a "
				."LEFT JOIN #__djcf_items i ON i.id=a.item_id "
				."WHERE a.user_id = ".$uid." ";
		$db->setQuery($query);
		$profile['msg_items']=$db->loadObjectList();
		
		$items_msg_xml = $profileXML->addChild('items_messages');
		if(count($profile['msg_items'])){
			foreach($profile['msg_items'] as $msg){				
				$item_msg_xml = $items_msg_xml->addChild('message');
				
				$item_msg_xml->addChild('id',$msg->id);
				$item_msg_xml->addChild('item_id',$msg->item_id);
				$item_msg_xml->addChild('item_name',$msg->i_name);												
				$item_msg_xml->addChild('date',$msg->date);
				$item_msg_xml->addChild('ip_address',$msg->ip_address);
				$item_msg_xml->addChild('custom_fields',$msg->custom_fields);
				$item_msg_xml->addChild('message',$msg->message);
			}
		}
		
		//echo '<pre>';print_r($profile['msg_items']);die();
		
		$query ="SELECT a.* , u.name as u_name FROM #__djcf_profiles_msg a "
				."LEFT JOIN #__users u ON u.id=a.user_to "
				."WHERE a.user_from = ".$uid." ";
		$db->setQuery($query);
		$profile['msg_profiles']=$db->loadObjectList();
		
		//echo '<pre>';print_r($profile['msg_profiles']);die();
		
		$profiles_msg_xml = $profileXML->addChild('profiles_messages');
		if(count($profile['msg_profiles'])){
			foreach($profile['msg_profiles'] as $msg){
				$profile_msg_xml = $profiles_msg_xml->addChild('message');
		
				$profile_msg_xml->addChild('id',$msg->id);
				$profile_msg_xml->addChild('user_id',$msg->user_to);
				$profile_msg_xml->addChild('user_name',$msg->u_name);
				$profile_msg_xml->addChild('date',$msg->date);
				$profile_msg_xml->addChild('ip_address',$msg->ip_address);
				$profile_msg_xml->addChild('custom_fields',$msg->custom_fields);
				$profile_msg_xml->addChild('message',$msg->message);
			}
		}
		
		
		//header('Content-type: text/xml');echo $profileXML->asXML();die();
		
		$xml_file_name = 'Export_Profile_'.$uid.'_'.date("Y-m-d_H_i_s").'.xml';
		$xml_file = JPATH_COMPONENT_ADMINISTRATOR.'/export/'.$xml_file_name;
		$profileXML->saveXML($xml_file);
			
		$xml_link = '<a target="_blank" href="'.JUri::base().'components/com_djclassifieds/export/'.$xml_file_name.'">'.$xml_file_name.'</a>';
		$this->setRedirect( 'index.php?option=com_djclassifieds&view=profiles', JText::_('COM_DJCLASSIFIEDS_XML_GENERATED').': '.$xml_link );
			
		
		//echo '<pre>';
		//print_r($profile);
		//echo $id;die();
		
	}
	
}