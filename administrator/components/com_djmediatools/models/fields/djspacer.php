<?php
/**
 * @version $Id$
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
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

JFormHelper::loadFieldClass('Spacer');

/**
 * Form Field class for the Joomla Platform.
 * Provides spacer markup to be used in form layouts.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldDJSpacer extends JFormFieldSpacer
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'Spacer';

    /**
     * Method to get the field input markup for a spacer.
     * The spacer does not have accept input.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        return '';
    }

    /**
     * Method to get the field label markup for a spacer.
     * Use the label text or name from the XML element as the spacer or
     * Use a hr="true" to automatically generate plain hr markup
     *
     * @return  string  The field label markup.
     *
     * @since   11.1
     */
    protected function getLabel()
    {
    	$module = 'com_djmediatools';
    	
    	if($module) {
	    	$lang = JFactory::getLanguage();
			$lang->load($module, JPATH_ROOT . '/administrator', 'en-GB', true, false);
	    	$lang->load($module, JPATH_ROOT . '/administrator/components/'.$module, 'en-GB', true, false);
	    	$lang->load($module, JPATH_ROOT . '/administrator', null, true, false);
	    	$lang->load($module, JPATH_ROOT . '/administrator/components/'.$module, null, true, false);
    	}
    	
    	$document = JFactory::getDocument();
    	$document->addStylesheet(JURI::root(true).'/administrator/components/com_djmediatools/assets/forms.css');
    	
        $html = array();
        $class = $this->element['class'] ? (string) $this->element['class'] : '';
        $type = $this->element['alert_type'] ? (string) $this->element['alert_type'] : 'info';
        
        $html[] = '<div class="' . $class . ' djspacer alert alert-'.$type.'">';
        
        // Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = $this->translateLabel ? JText::_($text) : $text;

		if($this->element['label']) $html[] = '<strong id="' . $this->id . '-lbl">' . $text . '</strong>';
		
        // If a description is specified, use it to build a tooltip.
        if (!empty($this->description))
        {
            $html[] = '<div class="small">'
                . ($this->translateDescription ? JText::_($this->description) : $this->description)
            	. '</div> ';
        }
        
        $html[] = '</div>';
        
        return implode('', $html);
    }

    /**
     * Method to get the field title.
     *
     * @return  string  The field title.
     *
     * @since   11.1
     */
    protected function getTitle()
    {
        return $this->getLabel();
    }
}
