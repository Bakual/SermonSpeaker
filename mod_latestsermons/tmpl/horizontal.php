<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
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
		<div class="row-fluid">
			<?php foreach ($list as $row) : ?>
				<?php $i++; ?>
				<div class="latestsermons_entry<?php echo $i; ?> text-center span<?php echo (int) 12 / $count; ?>">
					<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($row)) : ?>
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language)); ?>">
							<img src="<?php echo $picture; ?>" class="img-polaroid">
						</a>
					<?php endif; ?>
					<h3>
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language)); ?>">
							<?php echo $row->title; ?>
							<?php if ($params->get('show_hits', 0) > 1 and $row->hits) : ?>
								<small>(<?php echo $row->hits; ?>)</small>
							<?php endif; ?>
						</a>
					</h3>
					<?php if ($params->get('ls_show_mo_series') and $row->series_title) : ?>
						<span>
							<?php if ($row->series_state) : ?>
								<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($row->series_slug, $row->series_catid, $row->series_language)); ?>">
									<?php echo $row->series_title; ?></a>
							<?php else : ?>
								<?php echo $row->series_title; ?>
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<?php if ($params->get('ls_show_mo_speaker') and $row->speaker_title) : ?>
						<span>
							<?php if ($params->get('ls_show_mo_series') and $row->series_title) : ?>|<?php endif; ?>
							<?php if ($row->speaker_state): ?>
								<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($row->speaker_slug, $row->speaker_catid, $row->speaker_language)); ?>">
									<?php echo $row->speaker_title; ?>
								</a>
							<?php else : ?>
								<?php echo $row->speaker_title; ?>
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<?php if ($params->get('show_hits', 0) & 1) : ?>
						<div class="hits">
							<?php echo JText::_('JGLOBAL_HITS'); ?>: <?php echo $row->hits; ?>
						</div>
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
	<?php if ($params->get('ls_show_mo_link')) :
		if ($itemid) : ?>
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