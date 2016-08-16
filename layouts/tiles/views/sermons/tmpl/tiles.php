<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::stylesheet('com_sermonspeaker/tiles.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');
$user       = JFactory::getUser();
$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif;

	if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
		<h2>
			<?php echo $this->escape($this->params->get('page_subheading'));

			if ($this->params->get('show_category_title')) : ?>
				<span class="subheading-category"><?php echo $this->category->title; ?></span>
			<?php endif; ?>
		</h2>
	<?php endif;

	if ($this->params->get('show_description', 1) or $this->params->def('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') and $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;

			if ($this->params->get('show_description') and $this->category->description) :
				echo JHtml::_('content.prepare', $this->category->description);
			endif; ?>
			<div class="clr"></div>
		</div>
	<?php endif;

	if (in_array('sermons:player', $this->columns) and count($this->items)) :
		JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
		<div class="ss-sermons-player">
			<hr class="ss-sermons-player"/>
			<?php if (empty($player->hideInfo)): ?>
				<div id="playing">
					<img id="playing-pic" class="picture" src=""/>
					<span id="playing-duration" class="duration"></span>
					<div class="text">
						<span id="playing-title" class="title"></span>
						<span id="playing-desc" class="desc"></span>
					</div>
					<span id="playing-error" class="error"></span>
				</div>
			<?php endif;
			echo $player->mspace;
			echo $player->script;
			?>
			<hr class="ss-sermons-player"/>
			<?php if ($player->toggle) : ?>
				<div>
					<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video"
						 title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>"/>
					<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio"
						 title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>"/>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post"
		  id="adminForm" name="adminForm" class="form-inline">
		<?php
		if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
			echo $this->loadTemplate('filters');
		endif;

		if (!count($this->items)) : ?>
			<div
				class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
		<?php else : ?>
			<?php
			foreach ($this->items as $i => $item) :
				// Preparing tooltip
				$tip = array();

				if (in_array('sermons:num', $this->columns) and $item->sermon_number) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number;
				endif;

				if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00')) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL') . ': ' . JHtml::Date($item->sermon_date, JText::_($this->params->get('date_format')), true);
				endif;

				if (in_array('sermons:category', $this->columns)) :
					$tip[] = JText::_('JCATEGORY') . ': ' . $item->category_title;
				endif;

				if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL') . ': ' . $item->speaker_title;
				endif;

				if (in_array('sermons:series', $this->columns) and $item->series_title) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL') . ': ' . $item->series_title;
				endif;

				if (in_array('sermons:scripture', $this->columns) and $item->scripture) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false);
				endif;

				if (in_array('sermons:length', $this->columns) and $item->sermon_time) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time);
				endif;

				if (in_array('sermons:hits', $this->columns) and $item->hits) :
					$tip[] = JText::_('JGLOBAL_HITS') . ': ' . $item->hits;
				endif;

				if (in_array('sermons:notes', $this->columns) and $item->notes) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL') . ': ' . $item->notes;
				endif;
				$tooltip = implode('<br/>', $tip);
				$picture = SermonspeakerHelperSermonspeaker::insertPicture($item);

				if (!$picture) :
					$picture = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
				endif; ?>
				<div id="sermon<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText($item->title, $tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>">
					<img border="0" align="middle" src="<?php echo $picture; ?>">
					<span class="item-title">
						<?php echo $item->title; ?>
					</span>
				</a>
				</span>
				</div>
			<?php endforeach; ?>
			<div class="clear-left"></div>
		<?php endif;

		if ($this->params->get('show_pagination') && ($this->pagination->get('pages.total') > 1)) : ?>
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
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3>
				<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
			</h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
