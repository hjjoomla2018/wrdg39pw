<?php
/**
 * @version $Id: default.php 38 2015-04-14 08:03:47Z michal $
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

//JHtml::_('behavior.tooltip', '.djrv_tooltip');
JHtml::_('behavior.formvalidator');

$user = JFactory::getUser();
$app = JFactory::getApplication();
$params = JComponentHelper::getParams('com_djreviews');

$style = 'style="display: none;"';
if ($app->input->get('djreviews_action') == 'comment') {
	$style= '';
}


?>
<div id="djrv-your-comment-<?php echo (int)$this->form->getValue('item_id'); ?>" <?php echo $style; ?> class="djrv_comment_form djreviews djrv_clearfix">
<?php if ($user->authorise('core.create', 'com_djreviews') || ($user->authorise('comment.create', 'com_djreviews'))) { ?>

<div class="modal-backdrop djrv_modal-backdrop fade in"></div>
<div class="modal djrv_modal">
<div class="modal-header djrv_modal-header">
	<button type="button" class="djrv_close_comment_form close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3><?php echo JText::_('COM_DJREVIEWS_YOUR_COMMENT'); ?></h3>
</div>
<div class="modal-body djrv_modal-body">

<div id="djrv_comment_msg-<?php echo (int)$this->form->getValue('item_id'); ?>"></div>

<form action="<?php echo 'index.php'; /*(string)JUri::getInstance();*/ ?>" method="post" name="djrv_comment_form" id="djrv_comment_form-<?php echo (int)$this->form->getValue('item_id'); ?>" class="form-validate djrv_comment_form">
	
	<div class="row-fluid clearfix">
		<div class="span12 form-horizontal">
			<fieldset>
			<?php if ((int)$params->get('title', 2) > 0 ) {?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
			<?php } ?>
			<?php if ((int)$params->get('message', 2) > 0 ) {?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('message'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('message'); ?></div>
			</div>
			<?php } ?>
			
			<?php if ($user->guest) { ?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('user_name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('user_login'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_login'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
			</div>
			<?php } ?>
			</fieldset>
			
			<fieldset>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn button validate btn-success"><?php echo JText::_('COM_DJREVIEWS_SUBMIT');?></button>
						<button class="djrv_close_comment_form btn button"><?php echo JText::_('COM_DJREVIEWS_CLOSE')?></button>
					</div>
				</div>
			</fieldset>
		</div>
	<input type="hidden" name="task" value="comment.save" />
	<input type="hidden" name="option" value="com_djreviews" />
	<input type="hidden" name="id" value="<?php echo (int)$this->item->id; ?>" />
	
	<?php 
	JUri::reset();

	$uri = JUri::getInstance();
	$query = $uri->getQuery(true);
	foreach ($query as $k=>$v) {
		if (strpos($k, 'djreviews_') === 0) {
			unset($query[$k]);
		}
	}
	$uri->setQuery($query);
	$uri->setFragment(null);
	$return_url = $uri->toString();
	JUri::reset();

	?>
	
	<input type="hidden" name="return" value="<?php echo base64_encode($return_url)?>" />

	<?php echo $this->form->getInput('review_id'); ?>
	<?php echo $this->form->getInput('item_id'); ?>
	<?php echo $this->form->getInput('item_type'); ?>
		
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
	</div>
	
</form>
</div>
</div>
<div class="modal-footer djrv_modal-footer"></div>
<?php } ?>
</div>