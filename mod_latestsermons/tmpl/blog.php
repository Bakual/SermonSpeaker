<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var int                       $itemid
 * @var stdClass                  $module
 */

$i     = 0;
$count = count($list);
?>
<div class="latestsermons<?php echo $moduleclass_sfx; ?>">
	<?php if ($params->get('show_list')) : ?>
		<div class="latestsermons_list">
			<?php foreach ($list as $row) : ?>
				<?php $i++; ?>
				<?php if ($itemid) : ?>
					<?php $link = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id=' . $row->slug . '&Itemid=' . $itemid); ?>
				<?php else : ?>
					<?php $link = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language)); ?>
				<?php endif; ?>
				<div class="latestsermons_entry<?php echo $i; ?>">
					<h4><a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
							<?php if ($params->get('show_hits', 0) > 1 and $row->hits) : ?>
								<small>(<?php echo $row->hits; ?>)</small>
							<?php endif; ?>
						</a></h4>
					<dl class="article-info sermon-info" style="display:block; margin:0;">
						<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
						<?php if ($params->get('show_category') and $row->category_title) : ?>
							<dd class="category-name">
								<?php echo JText::_('JCATEGORY'); ?>:
								<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($row->catid, $row->language)); ?>">
									<?php echo $row->category_title; ?>
								</a>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_series') and $row->series_title) : ?>
							<dd class="category-name">
								<?php echo JText::_('MOD_LATESTSERMONS_SERIE'); ?>:
								<?php if ($row->series_state) : ?>
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($row->series_slug, $row->series_catid, $row->series_language)); ?>">
										<?php echo $row->series_title; ?>
									</a>
								<?php else : ?>
									<?php echo $row->series_title; ?>
								<?php endif; ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_date') and $row->sermon_date) : ?>
							<dd class="published">
								<?php $date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
								echo JText::_('JDATE') . ': ' . JHtml::date($row->sermon_date, $date_format, true); ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_speaker') and $row->speaker_title) : ?>
							<dd class="createdby">
								<?php echo JText::_('MOD_LATESTSERMONS_SPEAKER'); ?>:
								<?php if ($row->speaker_state): ?>
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($row->speaker_slug, $row->speaker_catid, $row->speaker_language)); ?>">
										<?php echo $row->speaker_title; ?>
									</a>
								<?php else :
									echo $row->speaker_title;
								endif; ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('show_hits', 0) & 1) : ?>
							<dd class="hits">
								<?php echo JText::_('JGLOBAL_HITS'); ?>:
								<?php echo $row->hits; ?>
							</dd>
						<?php endif; ?>
					</dl>
					<div style="clear:left;"></div>
					<?php if (strlen($row->notes) > 0) : ?>
						<div>
							<?php echo JHtml::_('content.prepare', $row->notes); ?>
						</div>
					<?php endif; ?>
					<?php if ($i < $count) : ?>
						<hr/>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ($params->get('show_player')) : ?>
		<?php if ($params->get('show_list')) : ?>
			<br/>
		<?php endif; ?>
		<div class="latestsermons_player">
			<?php $c_params       = JComponentHelper::getParams('com_sermonspeaker');
			$config['autostart']  = 0;
			$config['count']      = 'ls' . $module->id;
			$config['type']       = $c_params->get('fileprio') ? 'video' : 'audio';
			$config['alt_player'] = $c_params->get('alt_player');
			$config['vheight']    = $params->get('vheight');
			$player               = SermonspeakerHelperSermonspeaker::getPlayer($list, $config);
			echo $player->mspace;
			echo $player->script; ?>
		</div>
	<?php endif; ?>
	<?php if ($params->get('ls_show_mo_link')) : ?>
		<?php if ($itemid) : ?>
			<?php $link = 'index.php?option=com_sermonspeaker&view=sermons&Itemid=' . $itemid; ?>
		<?php else : ?>
			<?php $link = SermonspeakerHelperRoute::getSermonsRoute(); ?>
		<?php endif; ?>
		<br/>
		<div class="latestsermons_link">
			<a href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
		</div>
	<?php endif; ?>
</div>
