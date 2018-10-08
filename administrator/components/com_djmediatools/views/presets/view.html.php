<?php
/**
 * @version $Id: view.html.php 107 2017-09-20 11:14:14Z szymon $
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');
class DJMediatoolsViewPresets extends JViewLegacy
{
	protected $form;
	protected $item;

	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		
		$this->presets	= $this->get('Items');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$btnclass = 'btn btn-primary btn-large';
		$btn2class = 'btn btn-link btn-large';
		
		// include uploader events and simple managing of album items
		JHtml::_('behavior.framework');
		
		if(JRequest::getVar('tmpl')!='component') {
			$this->addToolbar();
		} else {
			$function = JRequest::getVar('f_name');
			
			//die('dupa przed');
			
			$this->button = "
				
				<script type='text/javascript'>
					function save2insert(cover) {
						
						if (document.formvalidator.isValid(document.id('item-form'))) {
							".$this->form->getField('description')->save()."
							document.getElementById('item-form').task.value='category.save';
							
							var loader = new Element('div', {
								styles: {
									background: '#fff url(components/com_djmediatools/assets/bigloader.gif) center center no-repeat',
									position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', 'z-index': 9999
								}
							});
							loader.fade('hide');
							
							document.id('item-form').set('send',{
								onRequest: function(){
									loader.inject(document.id(document.body));
									loader.fade(0.8);
								},
								onSuccess: function(responseText){
									
									var rsp = responseText.trim();
									if(rsp){
										var json = rsp;
										if(rsp.charAt(0) != '[' && rsp.charAt(0) != '{'){
											json = rsp.match(/{.*?}/);
											if(json && json[0]){
												json = json[0];
											}
										}
				
										if(json && typeof json == 'string'){
											json = JSON.decode(json);
										}
									
										if (window.parent) window.parent.".$function."(json.id,json.image,json.title, cover);
									}
									
									
								},
								onFailure: function(){
									loader.destroy();
								}
							});
									
							document.id('item-form').send();
						}
						else {
							alert('".$this->escape(JText::_('COM_DJMEDIATOOLS_VALIDATION_FORM_FAILED'))."');
						}
					}
				
					
				</script>
				
			";
			//if (window.parent) window.parent.".$function."('".$item->id."','".$item->image ? $item->image : $item->thumb."','".$this->escape($item->title)."');
			//die('dupa po');
			
			//die($this->button);
			$this->button .= '
				<div class="modalAlbum">
					<input type="hidden" name="tmpl" value="component" />
					<input type="hidden" name="f_name" value="'.$function.'" />
					<input type="button" class="'.$btnclass.'" value="'.JText::_('COM_DJMEDIATOOLS_MODAL_SAVE_INSERT').'" onclick="return save2insert();" />
					<input type="button" class="'.$btnclass.' hasTip" title="::'.JText::_('COM_DJMEDIATOOLS_INSERT_LINKED_COVER_DESC').'" value="'.JText::_('COM_DJMEDIATOOLS_MODAL_SAVE_INSERT_COVER').'" onclick="return save2insert(true);" />
					<a class="'.$btn2class.'" href="index.php?option=com_djmediatools&amp;view=categories&amp;layout=modal&amp;tmpl=component&amp;f_name='.$function.'">'.JText::_('JCANCEL').'</a>
				</div>';
		}
		
		if (class_exists('JHtmlSidebar')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DJMEDIATOOLS').' ›› '.JText::_('COM_DJMEDIATOOLS_SUBMENU_PRESETS'), 'presets');
		
		JToolBarHelper::preferences('com_djmediatools', 550, 900);
	}
}
