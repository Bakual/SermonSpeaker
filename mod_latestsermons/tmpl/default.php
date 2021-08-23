<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var int                       $itemid
 * @var stdClass                  $module
 */

$i       = 0;
$tooltip = ($params->get('ls_show_mo_speaker') || $params->get('ls_show_mo_series') || $params->get('ls_show_mo_date') || $params->get('show_hits') & 1);

if ($tooltip)
{
	// Include only if needed...
	HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
}
?>
<div class="latestsermons">
	<?php if ($params->get('show_list')) : ?>
		<ul class="latestsermons-list mod-list">
			<?php foreach ($list as $row) : ?>
				<?php $i++; ?>
				<li class="latestsermons_entry<?php echo $i; ?>">
					<?php if ($params->get('use_date')) : ?>
						<?php $date_format = Text::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4')); ?>
						<?php $text = HTMLHelper::date($row->sermon_date, $date_format, true); ?>
					<?php else : ?>
						<?php $text = $row->title; ?>
					<?php endif; ?>
					<?php if ($params->get('show_hits') > 1 and $row->hits) : ?>
						<?php $text .= ' <small>(' . $row->hits . ')</small>'; ?>
					<?php endif; ?>
					<?php if ($itemid) : ?>
						<?php $link = Route::_('index.php?option=com_sermonspeaker&view=sermon&id=' . $row->slug . '&Itemid=' . $itemid); ?>
					<?php else : ?>
						<?php $link = Route::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language)); ?>
					<?php endif; ?>
					<?php if ($tooltip) : ?>
						<?php $title = ''; ?>
						<?php $tips = array(); ?>
						<?php if ($params->get('show_tooltip_title')) : ?>
							<?php $title = $row->title; ?>
						<?php endif; ?>
						<?php if ($params->get('show_category') and $row->category_title) : ?>
							<?php $tips[] = Text::_('JCATEGORY') . ': ' . $row->category_title; ?>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_speaker') and $row->speaker_title) : ?>
							<?php $tips[] = Text::_('MOD_LATESTSERMONS_SPEAKER') . ': ' . $row->speaker_title; ?>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_series') and $row->series_title) : ?>
							<?php $tips[] = Text::_('MOD_LATESTSERMONS_SERIE') . ': ' . $row->series_title; ?>
						<?php endif; ?>
						<?php if ($params->get('ls_show_mo_date') and $row->sermon_date) : ?>
							<?php $date_format = Text::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4')); ?>
							<?php $tips[] = Text::_('JDATE') . ': ' . HTMLHelper::date($row->sermon_date, $date_format, true); ?>
						<?php endif; ?>
						<?php if ($params->get('show_scripture') and $row->scripture) : ?>
							<?php $tips[] = Text::_('MOD_LATESTSERMONS_SCRIPTURE') . ': ' . SermonspeakerHelperSermonspeaker::insertScriptures($row->scripture, ', ', false);  ?>
						<?php endif; ?>
						<?php if (($params->get('show_hits') & 1) and $row->hits) : ?>
							<?php $tips[] = Text::_('JGLOBAL_HITS') . ': ' . $row->hits; ?>
						<?php endif; ?>
						<?php $tip = implode('<br/>', $tips); ?>
						<?php echo HTMLHelper::tooltip($tip, $title, '', $text, $link); ?>
					<?php else : ?>
						<a href="<?php echo $link; ?>">
							<?php echo $text; ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ($params->get('show_player')) : ?>
		<div class="latestsermons_player">
			<?php $c_params       = ComponentHelper::getParams('com_sermonspeaker');
			$config['autostart']  = 0;
			$config['count']      = 'ls' . $module->id;
			$config['type']       = $c_params->get('fileprio') ? 'video' : 'audio';
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
			<a href="<?php echo Route::_($link); ?>"><?php echo Text::_('MOD_LATESTSERMONS_LINK'); ?></a>
		</div>
	<?php endif; ?>
</div>
