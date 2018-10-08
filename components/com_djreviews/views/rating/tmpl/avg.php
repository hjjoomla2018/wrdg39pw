<?php
/**
 * @package DJ-Reviews
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
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

/* Basic AVG rating */
$item = $this->item;

if (empty($item->rating_fields)) {
	return false;
}

$params = DJReviewsHelper::getParams($this->item->rating_group_id);
$schemaWrapper = $params->get('schema_wrapper', '');
?>

<div class="djrv_rating_avg djreviews" id="djrv-rating-avg-<?php echo $item->id; ?>" data-wrapper="djrv-avg-<?php echo $item->id; ?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" data-object="<?php echo $item->id; ?>">
	<?php if ($schemaWrapper) {?>
		<div itemscope itemtype="http://schema.org/<?php echo $schemaWrapper;?>">
		<meta itemprop="name" content="<?php echo $this->escape($item->name); ?>" />
		<?php } ?>
	<div class="djrv_item_rating djrv_rating small">
		<span class="djrv_stars">
			<?php /*for ($i = 1; $i <= 5; $i++) {?>
			<span class="djrv_star <?php if ($i <= $item->avg_rate || ($item->avg_rate - $i) >= -0.5) echo 'active';?>"></span>
			<?php }*/ ?>
			<span class="djrv_stars_sprite"><span class="djrv_stars_sprite_rating" style="width:<?php echo 100 * ($item->avg_rate / 5); ?>%;"></span></span>
		</span>
		<span class="djrv_avg small">
			<?php echo $item->avg_rate; ?> <span class="djrv_vote_cnt">( <?php 
			echo JText::sprintf('COM_DJREVIEWS_VOTE_COUNT', $item->rat_count);
			?> )</span>
		</span>
		<meta itemprop="ratingValue" content="<?php echo $item->avg_rate;?>" />
		<meta itemprop="reviewCount" content="<?php echo $item->rat_count; ?>" />
		<meta itemprop="name" content="<?php echo $this->escape($item->name); ?>" />
		<meta itemprop="worstRating" content="1"/>
      	<meta itemprop="bestRating" content="5"/>
	</div>
	
	<?php if ($schemaWrapper) {?>
	</div>
	<?php } ?>
	
</div>

