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

$user = JFactory::getUser();
$params = JComponentHelper::getParams('com_djreviews');
$item = $this->item;

?>
<div class="djrv_comments row-striped">
	<?php foreach ($this->item->comments as $comment) { ?>
		<div class="djrv_comment djrv_clearfix">
			
			<?php if ($comment->title && (int)$params->get('title', 2) > 0) {?>
			<h4><?php echo $comment->title; ?></h4>
			<?php } ?>
						
			<div class="djrv_post_info">
				<div class="djrv_poster small">
					<?php echo JText::sprintf('COM_DJREVIEWS_POSTED_BY_ON', '<span>'.$comment->user_login.'</span>', JHtml::_('date', $comment->created, $params->get('date_format', 'd-m-Y H:i'))); ?>
				</div>
			</div>
			
			<?php if ($comment->message && (int)$params->get('message', 2) > 0) {?>			
			<p class="djrv_message_quote"><?php echo nl2br($comment->message); ?></p>
			<?php } ?>
			
			<?php
			
			$canEdit = ($user->id > 0 && $item->created_by == $user->id && $user->authorise('comment.edit.own', 'com_djreviews')) || $user->authorise('core.edit', 'com_djreviews');
			$canDelete = ($user->id > 0 && $item->created_by == $user->id && $user->authorise('comment.delete.own', 'com_djreviews')) || $user->authorise('core.delete', 'com_djreviews');
			
			if ($canEdit || $canDelete) { ?>
			<div class="djrv_review_toolbar pull-right">
				<div class="btn-toolbar">
					<div class="btn-group">
					<?php 
					if ($canEdit) {
						JUri::reset();
						$uri = JUri::getInstance($this->review_object->link);
						$query = $uri->getQuery(true);
						$query['djreviews_action'] = 'edit_comment';
						$query['djreviews_review'] = $item->id;
						$query['djreviews_comment'] = $comment->id;
						$uri->setQuery($query);
						$uri->setFragment('your-review');
						$edit_link = $uri->toString();
						JUri::reset();
					?>
					<a class="djrv_comment_edit btn button btn-mini" href="<?php echo $edit_link; ?>" data-action="edit" data-review_id="<?php echo $item->id; ?>" data-id="<?php echo $comment->id; ?>"><?php echo JText::_('COM_DJREVIEWS_EDIT_COMMENT'); ?></a>
					<?php } ?>
					
					<?php 
					if ($canDelete) {
						$uri = JRoute::_($this->review_object->link);
						$remove_link = JRoute::_('index.php?option=com_djreviews&task=comment.delete&id='.$comment->id.'&return='.base64_encode($uri).'&'.JSession::getFormToken().'=1');
					?>
					<a class="djrv_comment_delete btn button btn-mini btn-danger" href="<?php echo $remove_link; ?>" data-action="delete" data-review_id="<?php echo $item->id; ?>" data-id="<?php echo $comment->id; ?>"><?php echo JText::_('COM_DJREVIEWS_DELETE_COMMENT'); ?></a>
					<?php } ?>
					
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<?php
