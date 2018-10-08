<?php
/**
* @version		1.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Payment Plugin
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');
$lang = JFactory::getLanguage();
$lang->load('plg_djclassifiedspayment_djcfPaypal',JPATH_ADMINISTRATOR);
require_once(JPATH_BASE.DS.'administrator/components/com_djclassifieds/lib/djseo.php');
require_once(JPATH_BASE.DS.'administrator/components/com_djclassifieds/lib/djnotify.php');
require_once(JPATH_BASE.DS.'administrator/components/com_djclassifieds/lib/djpayment.php');


class plgdjclassifiedspaymentdjcfPaypal extends JPlugin
{
	function __construct( &$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_djcfPaypal');
		$params["plugin_name"] = "djcfPaypal";
		$params["icon"] = "paypal_icon.png";
		$params["logo"] = "paypal_overview.png";
		$params["description"] = JText::_("PLG_DJCFPAYPAL_PAYMENT_METHOD_DESC");
		$params["payment_method"] = JText::_("PLG_DJCFPAYPAL_PAYMENT_METHOD_NAME");
		$params["testmode"] = $this->params->get("test");
		$params["currency_code"] = $this->params->get("currency_code");
		$params["email_id"] = $this->params->get("email_id");
		$params["image_url"] = $this->params->get("image_url");
		$this->params = $params;

	}
	function onProcessPayment()
	{
		$ptype = JRequest::getVar('ptype','');
		$id = JRequest::getInt('id','0');
		$html="";

			
		if($ptype == $this->params["plugin_name"])
		{
			$action = JRequest::getVar('pactiontype','');
			switch ($action)
			{
				case "process" :
				$html = $this->process($id);
				break;
				case "notify" :
				$html = $this->_notify_url();
				break;
				case "paymentmessage" :
				$html = $this->_paymentsuccess();
				break;
				default :
				$html =  $this->process($id);
				break;
			}
		}
		return $html;
	}
	function _notify_url()
	{
		$db = JFactory::getDBO();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$account_type=$this->params["testmode"];
		$user	= JFactory::getUser();
		$id	= JRequest::getInt('id','0');		

		$paypal_info = $_POST;
	
		/*$fil = fopen('ppraport/pp_raport.txt', 'a');
		fwrite($fil, "\n\n--------------------post_first-----------------\n");
		$post = $_POST;
		foreach ($post as $key => $value) {
		fwrite($fil, $key.' - '.$value."\n");
		}
		fclose($fil);*/

		$paypal_ipn = new paypal_ipn($paypal_info);
		foreach ($paypal_ipn->paypal_post_vars as $key=>$value)
		{
			if (getType($key)=="string")
			{
				eval("\$$key=\$value;");
			}
		}
		$paypal_ipn->send_response($account_type);
		if (!$paypal_ipn->is_verified())
		{
			die('');
		}
		$paymentstatus=0;

			$status = $paypal_ipn->get_payment_status();
			$txn_id=$paypal_ipn->paypal_post_vars['txn_id'];
			
			if(($status=='Completed') || ($status=='Pending' && $account_type==1)){				
				
				
				DJClassifiedsPayment::completePayment($id, JRequest::getVar('mc_gross'), $txn_id);
			
			}else{
				$query = "UPDATE #__djcf_payments SET status='".$status."',transaction_id='".$txn_id."' "
						."WHERE id=".$id." AND method='djcfPaypal'";					
				$db->setQuery($query);
				$db->query();	
			}
				
		
		
		
	}
	
	function process($id)
	{
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');		
		jimport( 'joomla.database.table' );
		$db 	= JFactory::getDBO();
		$app 	= JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid",'0');
		$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
		$user 	= JFactory::getUser();
		$ptype	= JRequest::getVar('ptype');
		$type	= JRequest::getVar('type','');
		$row 	= JTable::getInstance('Payments', 'DJClassifiedsTable');	
		$paypal_email = $this->params["email_id"];

		$pdetails = DJClassifiedsPayment::processPayment($id, $type,$ptype);				

		if($type=='order'){
			$query ="SELECT o.* FROM #__djcf_orders o "
					."WHERE o.id=".$id." LIMIT 1";
			$db->setQuery($query);
			$order = $db->loadObject();
			
			$query ="SELECT i.*, c.price as c_price FROM #__djcf_items i "
					."LEFT JOIN #__djcf_categories c ON c.id=i.cat_id "
					."WHERE i.id=".$order->item_id." LIMIT 1";
			$db->setQuery($query);
			$item = $db->loadObject();
			if(!isset($item)){
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_AD');
				$redirect="index.php?option=com_djclassifieds&view=items&cid=0";
			}
			
			$paypay_user = DJClassifiedsPayment::getUserPaypal($item->user_id);
			
			if($paypay_user){
				$paypal_email = $paypay_user;
			}
		}else if($type=='offer'){
			$query ="SELECT o.* FROM #__djcf_offers o "
					."WHERE o.id=".$id." LIMIT 1";
			$db->setQuery($query);
			$order = $db->loadObject();
			
			$query ="SELECT i.*, c.price as c_price FROM #__djcf_items i "
					."LEFT JOIN #__djcf_categories c ON c.id=i.cat_id "
					."WHERE i.id=".$order->item_id." LIMIT 1";
			$db->setQuery($query);
			$item = $db->loadObject();
			if(!isset($item)){
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_AD');
				$redirect="index.php?option=com_djclassifieds&view=items&cid=0";
			}
			
			$paypay_user = DJClassifiedsPayment::getUserPaypal($item->user_id);
			
			if($paypay_user){
				$paypal_email = $paypay_user;
			}
		}
		
		$urlpaypal="";
		if ($this->params["testmode"]=="1"){
			$urlpaypal="https://www.sandbox.paypal.com/cgi-bin/webscr";
		}elseif ($this->params["testmode"]=="0"){
			$urlpaypal="https://www.paypal.com/cgi-bin/webscr";
		}
		header("Content-type: text/html; charset=utf-8");
		echo JText::_('PLG_DJCFPAYPAL_REDIRECTING_PLEASE_WAIT');
		$form ='<form id="paypalform" action="'.$urlpaypal.'" method="post">';
		$form .='<input type="hidden" name="cmd" value="_xclick">';
		$form .='<input id="custom" type="hidden" name="custom" value="'.$pdetails['item_id'].'">';
		$form .='<input type="hidden" name="business" value="'.$paypal_email.'">';
		$form .='<input type="hidden" name="currency_code" value="'.$this->params["currency_code"].'">';
		$form .='<input type="hidden" name="item_name" value="'.$pdetails['itemname'].'">';
		$form .='<input type="hidden" name="amount" value="'.$pdetails['amount'].'">';
		$form .='<input type="hidden" name="charset" value="utf-8">';		
		if($this->params["image_url"]){
			$form .='<input type="hidden" name="image_url" value="'.JURI::root().$this->params["image_url"].'">';
			$form .='<input type="hidden" name="page_style" value="paypal" />';
		}		
		$form .='<input type="hidden" name="cancel_return" value="'.JRoute::_(JURI::root().'index.php?option=com_djclassifieds&task=paymentReturn&r=error&id='.$pdetails['item_id'].$pdetails['item_cid'].'&Itemid='.$Itemid).'">';
		$form .='<input type="hidden" name="notify_url" value="'.JRoute::_(JURI::root().'index.php?option=com_djclassifieds&task=processPayment&ptype='.$this->params["plugin_name"].'&pactiontype=notify&id='.$pdetails['item_id']).'">';
		$form .='<input type="hidden" name="return" value="'.JRoute::_(JURI::root().'index.php?option=com_djclassifieds&task=paymentReturn&r=ok&id='.$pdetails['item_id'].$pdetails['item_cid'].'&Itemid='.$Itemid).'">';
		$form .='</form>';
		echo $form;
	?>
		<script type="text/javascript">
			callpayment()
			function callpayment(){
				var id = document.getElementById('custom').value ;
				if ( id > 0 && id != '' ) {
					document.getElementById('paypalform').submit();
				}
			}
		</script>
	<?php
	}

	function onPaymentMethodList($val)
	{		
		if($val["direct_payment"] && !$val["payment_email"]){
			return null;
		}
		
		$type='';
		if($val['type']){
			$type='&type='.$val['type'];	
		}		
		$html ='';
		if($this->params["email_id"]!=''){
			$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->params["plugin_name"]."/".$this->params["plugin_name"]."/images/".$this->params["logo"];
			//$form_action = JRoute :: _("index.php?option=com_djclassifieds&task=processPayment&ptype=".$this->params["plugin_name"]."&pactiontype=process&id=".$val["id"].$type, false);
			$form_action = JURI::root()."index.php?option=com_djclassifieds&task=processPayment&ptype=".$this->params["plugin_name"]."&pactiontype=process&id=".$val["id"].$type;
			$html ='<table cellpadding="5" cellspacing="0" width="100%" border="0">
				<tr>';
					if($this->params["logo"] != ""){
				$html .='<td class="td1" width="160" align="center">
						<img src="'.$paymentLogoPath.'" title="'. $this->params["payment_method"].'"/>
					</td>';
					 }
					$html .='<td class="td2">
						<h2>PAYPAL</h2>
						<p style="text-align:justify;">'.$this->params["description"].'</p>
					</td>
					<td class="td3" width="130" align="center">
						<a class="button" style="text-decoration:none;" href="'.$form_action.'">'.JText::_('COM_DJCLASSIFIEDS_BUY_NOW').'</a>
					</td>
				</tr>
			</table>';
		}
		return $html;
	}
}
class paypal_ipn
{
	var $paypal_post_vars;
	var $paypal_response;
	var $timeout;
	var $error_email;
	function __construct($paypal_post_vars) {
		$this->paypal_post_vars = $paypal_post_vars;
		$this->timeout = 120;
	}
	function send_response($account_type)
	{
		/*
		$fp  = '';
		if($account_type == '1')
		{
			$fp = @fsockopen( "www.sandbox.paypal.com", 80, $errno, $errstr, 120 );
		}else if($account_type == '0')
		{
			//$fp = @fsockopen( "www.paypal.com", 80, $errno, $errstr, 120 );
			$fp = @fsockopen( "ssl://www.paypal.com", 443, $errno, $errstr, 30 );
		}
		if (!$fp) {
			$this->error_out("PHP fsockopen() error: " . $errstr , "");
		} else {
			foreach($this->paypal_post_vars AS $key => $value) {
				if (@get_magic_quotes_gpc()) {
					$value = stripslashes($value);
				}
				$values[] = "$key" . "=" . urlencode($value);
			}
			$response = @implode("&", $values);
			$response .= "&cmd=_notify-validate";
			fputs( $fp, "POST /cgi-bin/webscr HTTP/1.0\r\n" );
			fputs( $fp, "Content-type: application/x-www-form-urlencoded\r\n" );
			fputs( $fp, "Content-length: " . strlen($response) . "\r\n\n" );
			fputs( $fp, "$response\n\r" );
			fputs( $fp, "\r\n" );
			$this->send_time = time();
			$this->paypal_response = "";

			while (!feof($fp)) {
				$this->paypal_response .= fgets( $fp, 1024 );

				if ($this->send_time < time() - $this->timeout) {
					$this->error_out("Timed out waiting for a response from PayPal. ($this->timeout seconds)" , "");
				}
			}
			fclose( $fp );
		}*/
		
		
		
		
		
		$req = 'cmd=_notify-validate';
			if (function_exists('get_magic_quotes_gpc')) {
			  $get_magic_quotes_exists = true;
			}
			//print_r($this->paypal_post_vars);
			foreach ($this->paypal_post_vars as $key => $value) {
			  if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			    $value = urlencode(stripslashes($value));
			  } else {
			    $value = urlencode($value);
			  }
			  $req .= "&$key=$value";
			}
			
			// Step 2: POST IPN data back to PayPal to validate
			$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			
			// In wamp-like environments that do not come bundled with root authority certificates,
			// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
			// the directory path of the certificate as shown below:
			// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
			
			if ( !($res = curl_exec($ch)) ) {
			   //error_log("Got " . curl_error($ch) . " when processing IPN data"); 
			  curl_close($ch);
			  exit;
			}
			curl_close($ch);
			$this->paypal_response = $res; 
		
		
		
		
		
	}
	function is_verified() {
		if( strstr($this->paypal_response,"VERIFIED") )
			return true;
		else
			return false;
	}
	function get_payment_status() {
		return $this->paypal_post_vars['payment_status'];
	}
	function error_out($message)
	{

	}
}
?>