<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$i = 0;
$tooltip = ($params->get('ls_show_mo_speaker') || $params->get('ls_show_mo_series') || $params->get('ls_show_mo_date') || $params->get('show_hits') & 1);

if ($tooltip)
{
	// Include only if needed...
	JHtml::_('bootstrap.tooltip');
}
?>
<div class="latestsermons<?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('show_list')) : ?>
	<ul class="latestsermons_list">
		<?php foreach($list as $row) : ?>
			<?php $i++; ?>
			<li class="latestsermons_entry<?php echo $i; ?>">
				<?php
				if ($params->get('use_date')) :
					$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
					$text = JHtml::Date($row->sermon_date, $date_format, true);
				else :
					$text = $row->title;
				endif;

				if ($params->get('show_hits') > 1 and $row->hits) :
					$text .= ' <small>(' . $row->hits . ')</small>';
				endif;

				if ($itemid) :
					$link = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id=' . $row->slug . '&Itemid=' . $itemid);
				else :
					$link = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language));
				endif;

				if ($tooltip) :
					$title = '';
					$tips = array();

					if ($params->get('show_tooltip_title')) :
						$title = $row->title;
					endif;

					if ($params->get('show_category') and $row->category_title) :
						$tips[] = JText::_('JCATEGORY') . ': ' . $row->category_title;
					endif;

					if ($params->get('ls_show_mo_speaker') and $row->speaker_title) :
						$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER') . ': ' . $row->speaker_title;
					endif;

					if ($params->get('ls_show_mo_series') and $row->series_title) :
						$tips[] = JText::_('MOD_LATESTSERMONS_SERIE') . ': ' . $row->series_title;
					endif;

					if ($params->get('ls_show_mo_date') and $row->sermon_date) :
						$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
						$tips[] = JText::_('JDATE') . ': ' . JHtml::Date($row->sermon_date, $date_format, true);
					endif;

					if (($params->get('show_hits') & 1) and $row->hits) :
						$tips[] = JText::_('JGLOBAL_HITS') . ': ' . $row->hits;
					endif;

					$tip = implode('<br />', $tips);
					echo JHtml::tooltip($tip, $title, '', $text, $link); ?>
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
		<?php
		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php';
		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';
		jimport('joomla.application.component.helper');
		$c_params = JComponentHelper::getParams('com_sermonspeaker');
		$config['autostart']  = 0;
		$config['count']      = 'ls';
		$config['type']       = $c_params->get('fileprio') ? 'video' : 'audio';
		$config['alt_player'] = $c_params->get('alt_player');
		$config['vheight']    = $params->get('vheight');
		$player = SermonspeakerHelperSermonspeaker::getPlayer($list, $config);
		echo $player->mspace;
		echo $player->script; ?>
	</div>
<?php endif; ?>
<?php if ($params->get('ls_show_mo_link')) : ?>
	<?php if ($itemid) :
		$link = 'index.php?option=com_sermonspeaker&view=sermons&Itemid=' . $itemid;
	else :
		$link = SermonspeakerHelperRoute::getSermonsRoute();
	endif; ?>
	<br />
	<div class="latestsermons_link">
		<a href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
	</div>
<?php endif; ?>
</div>
