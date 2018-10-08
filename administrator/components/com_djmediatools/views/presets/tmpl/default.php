<?php
/**
 * @version $Id: edit.php 101 2017-08-24 12:18:13Z szymon $
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

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.framework');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$preset = $app->input->getCmd('preset','');
$forceNew = count($this->presets) == 0;
?>

<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else: ?>
<div id="j-main-container">
<?php endif;?>

<form action="<?php echo JRoute::_('index.php?option=com_djmediatools&view=presets'); ?>" method="post" name="adminForm" id="preset-form" class="form-horizontal">
		
		<div class="control-group group_save">
			<div class="control-label">
				<label for="preset"><?php echo JText::_('COM_DJMEDIATOOLS_PRESET_LABEL') ?></label>
			</div>
			<div class="controls toolbar">
				<?php echo JHtml::_('select.genericlist', $this->presets, 'preset', 'onchange="this.form.submit()"', 'value', 'text', $preset, 'preset'); ?>
				<button class="btn btn-success preset_save" onclick="return Joomla.submitbutton('presets.save')"><i class="icon-save"></i>&nbsp;&nbsp;<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_SAVE') ?></button>
				<button class="btn btn-danger preset_remove" onclick="return Joomla.submitbutton('presets.delete')"><i class="icon-trash"></i>&nbsp;&nbsp;<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_REMOVE') ?></button>
			</div>

		</div>
		
		<div class="control-group group_save2copy">
			<div class="control-label">
				<label for="preset_name"><?php echo JText::_('COM_DJMEDIATOOLS_PRESET_NAME') ?></label>
			</div>
			<div class="controls toolbar">
				<input type="text" name="preset_name" id="preset_name" class="input-medium" placeholder="<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_NAME_HINT') ?>" />
				<?php if(!empty($preset)) { ?>
					<button class="btn btn-primary preset_save_copy" onclick="return Joomla.submitbutton('presets.save2copy')" ><i class="icon-copy"></i>&nbsp;&nbsp;<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_SAVE_COPY') ?></button>
				<?php } else { ?>
					<button class="btn btn-primary preset_save_new" onclick="return Joomla.submitbutton('presets.save2copy')"><i class="icon-new"></i>&nbsp;&nbsp;<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_SAVE_NEW') ?></button>
				<?php } ?>
			</div>
		</div>
				
		<?php $fieldSets = $this->form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet) : ?>
		<fieldset class="adminform">
		
		<legend><?php echo JText::_($fieldSet->label) ?></legend>
		
		<?php if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<div class="alert alert-info">'.$this->escape(JText::_($fieldSet->description)).'</div>';
		endif; ?>

		<?php foreach ($this->form->getFieldset($name) as $field) :  ?>
			<?php echo $field->renderField(); ?>
		<?php endforeach; ?>
		</fieldset>
		<?php endforeach; ?>
		
		<?php if(isset($this->button)) echo $this->button; ?>
		
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	
</form>

</div>

<div class="clearfix"></div>

<script type="text/javascript">
		
Joomla.submitbutton = function(task) {
console.log(task);
	switch(task) {

		case 'presets.save':
			if(jQuery('#preset').val()) {
				Joomla.submitform(task, document.getElementById('preset-form'));
			}
			break;
		case 'presets.save2copy':
			if(jQuery('#preset_name').val()) {
				Joomla.submitform(task, document.getElementById('preset-form'));
			}
			break;
		case 'presets.delete':
			if(jQuery('#preset').val() && confirm('<?php echo JText::_('COM_DJMEDIATOOLS_PRESET_REMOVE_CONFIRMATION') ?>')) {
				Joomla.submitform(task, document.getElementById('preset-form'));
			}
			break;
	}

	return false;
};

</script>