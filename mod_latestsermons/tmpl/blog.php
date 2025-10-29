<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper;

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var int                       $itemid
 * @var stdClass                  $module
 */

$i     = 0;
$count = count($list);

if ($params->get('show_player'))
{
	$c_params            = ComponentHelper::getParams('com_sermonspeaker');
	$config['autostart'] = 0;
	$config['count']     = 'ls' . $module->id;
	$config['type']      = $c_params->get('fileprio') ? 'video' : 'audio';
	$config['vheight']   = $params->get('vheight');
	$player              = SermonspeakerHelper::getPlayer($list, $config);
}
?>
<div class="latestsermons">
	<?php if ($params->get('show_list')) : ?>
		<div class="latestsermons_list">
			<?php foreach ($list as $i => $row) : ?>
				<?php if ($itemid) : ?>
					<?php $link = Route::_('index.php?option=com_sermonspeaker&view=sermon&id=' . $row->slug . '&Itemid=' . $itemid); ?>
				<?php else : ?>
					<?php $link = Route::_(RouteHelper::getSermonRoute($row->slug, $row->catid, $row->language)); ?>
				<?php endif; ?>
				<div class="latestsermons_entry<?php echo $i; ?>">
					<h4><a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
							<?php if ($params->get('show_hits', 0) > 1 and $row->hits) : ?>
								<small>(<?php echo $row->hits; ?>)</small>
							<?php endif; ?>
						</a></h4>
					<dl class="article-info sermon-info text-muted">
						<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
						<?php if ($params->get('show_category') and $row->category_title) : ?>
							<dd class="category-name">
								<?php echo Text::_('JCATEGORY'); ?>:
								<a href="<?php echo Route::_(RouteHelper::getSermonsRoute($row->catid, $row->language)); ?>">
									<?php echo $row->category_title; ?>
								</a>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_series') and $row->series_title) : ?>
							<dd class="category-name">
								<?php echo Text::_('MOD_LATESTSERMONS_SERIE'); ?>:
								<?php if ($row->series_state) : ?>
									<a href="<?php echo Route::_(RouteHelper::getSerieRoute($row->series_slug, $row->series_catid, $row->series_language)); ?>">
										<?php echo $row->series_title; ?>
									</a>
								<?php else : ?>
									<?php echo $row->series_title; ?>
								<?php endif; ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_date') and $row->sermon_date) : ?>
							<dd class="published">
								<?php $date_format = Text::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
								echo Text::_('JDATE') . ': ' . HTMLHelper::date($row->sermon_date, $date_format); ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_speaker') and $row->speaker_title) : ?>
							<dd class="createdby">
								<?php echo Text::_('MOD_LATESTSERMONS_SPEAKER'); ?>:
								<?php if ($row->speaker_state): ?>
									<a href="<?php echo Route::_(RouteHelper::getSpeakerRoute($row->speaker_slug, $row->speaker_catid, $row->speaker_language)); ?>">
										<?php echo $row->speaker_title; ?>
									</a>
								<?php else :
									echo $row->speaker_title;
								endif; ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('show_scripture') and $row->scripture) : ?>
							<dd class="scripture">
								<?php echo Text::_('MOD_LATESTSERMONS_SCRIPTURE'); ?>:
								<ul>
									<li>
										<?php echo SermonspeakerHelper::insertScriptures($row->scripture, '</li><li>'); ?>
									</li>
								</ul>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('show_hits', 0) & 1) : ?>
							<dd class="hits">
								<?php echo Text::_('JGLOBAL_HITS'); ?>:
								<?php echo $row->hits; ?>
							</dd>
						<?php endif; ?>
						<?php if ($params->get('show_player') and $params->get('control_player')) : ?>
							<?php $title = Text::_('MOD_LATESTSERMONS_PLAYICON_HOOVER');
							$playerId = !empty($player->id) ? $player->id : ''; ?>
							<dd class="sermonplay">
								<span class="fas fa-play pointer ss-play hasTooltip" data-id="<?php echo $i; ?>" data-player="<?php echo $playerId; ?>" title="<?php echo $title; ?>"> </span>
								<span class="pointer ss-play" data-id="<?php echo $i; ?>" data-player="<?php echo $playerId; ?>"><?php echo $title; ?></span>
							</dd>
						<?php endif; ?>
					</dl>
					<div style="clear:left;"></div>
					<?php if (strlen($row->notes) > 0) : ?>
						<div>
							<?php echo HTMLHelper::_('content.prepare', $row->notes); ?>
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
		<?php HTMLHelper::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true)); ?>
		<?php if ($params->get('show_list')) : ?>
			<br/>
		<?php endif; ?>
		<div class="latestsermons_player">
			<?php echo $player->mspace;
			echo $player->script; ?>
		</div>
	<?php endif; ?>
	<?php if ($params->get('ls_show_mo_link')) : ?>
		<?php if ($itemid) : ?>
			<?php $link = 'index.php?option=com_sermonspeaker&view=sermons&Itemid=' . $itemid; ?>
		<?php else : ?>
			<?php $link = RouteHelper::getSermonsRoute(); ?>
		<?php endif; ?>
		<br/>
		<div class="latestsermons_link">
			<a href="<?php echo Route::_($link); ?>"><?php echo Text::_('MOD_LATESTSERMONS_LINK'); ?></a>
		</div>
	<?php endif; ?>
</div>
