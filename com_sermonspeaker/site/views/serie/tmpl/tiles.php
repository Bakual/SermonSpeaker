<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
HTMLHelper::_('stylesheet', 'com_sermonspeaker/tiles.css', array('relative' => true));

$user       = Factory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="com-sermonspeaker-serie<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-serie-tiles">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'serie')); ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="com-sermonspeaker-serie__sermons">
		<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
			<?php echo $this->loadTemplate('filters'); ?>
		<?php endif; ?>
		<div class="clearfix"></div>

		<?php if (!count($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
			</div>
		<?php else : ?>
			<?php foreach ($this->items as $i => $item) :
				// Preparing tooltip
				$tip = array();

				if (in_array('sermons:num', $this->columns) and $item->sermon_number) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number;
				endif;

				if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00')) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL') . ': ' . HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true);
				endif;

				if (in_array('sermons:category', $this->columns)) :
					$tip[] = Text::_('JCATEGORY') . ': ' . $item->category_title;
				endif;

				if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL') . ': ' . $item->speaker_title;
				endif;

				if (in_array('sermons:series', $this->columns) and $item->series_title) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL') . ': ' . $item->series_title;
				endif;

				if (in_array('sermons:scripture', $this->columns) and $item->scripture) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false);
				endif;

				if (in_array('sermons:length', $this->columns) and $item->sermon_time) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time);
				endif;

				if (in_array('sermons:hits', $this->columns) and $item->hits) :
					$tip[] = Text::_('JGLOBAL_HITS') . ': ' . $item->hits;
				endif;

				if (in_array('sermons:notes', $this->columns) and $item->notes) :
					$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL') . ': ' . $item->notes;
				endif;
				$tooltip = implode('<br/>', $tip);
				$picture = SermonspeakerHelperSermonspeaker::insertPicture($item);

				if (!$picture) :
					$picture = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
				endif; ?>
				<div id="sermon<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTooltip"
					title="<?php echo HTMLHelper::tooltipText($item->title, $tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catid, $item->language)); ?>">
					<img border="0" align="middle" src="<?php echo trim($picture, '/'); ?>">
					<span class="item-title">
						<?php echo $item->title; ?>
					</span>
				</a>
				</span>
				</div>
			<?php endforeach; ?>
			<div class="clear-left"></div>
		<?php endif;

		if ($this->params->get('show_pagination') && ($this->pagination->pagesTotal > 1)) : ?>
			<div class="pagination">
				<?php if ($this->params->get('show_pagination_results', 1)) : ?>
					<p class="counter">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif;
				echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
</div>
