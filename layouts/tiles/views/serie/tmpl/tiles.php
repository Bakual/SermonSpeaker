<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('stylesheet', 'com_sermonspeaker/tiles.css', array('relative' => true));
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');
$user       = JFactory::getUser();
$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-serie-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<h2>
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>"><?php echo $this->item->title; ?></a>
	</h2>
	<?php
	if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
		<ul class="actions">
			<li class="edit-icon">
				<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?>
			</li>
		</ul>
	<?php endif;

	if ($this->params->get('show_category_title', 0) or in_array('serie:hits', $this->col_serie) or in_array('serie:speaker', $this->col_serie)) : ?>
		<dl class="article-info serie-info">
			<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
			<?php
			if ($this->params->get('show_category_title', 0)) : ?>
				<dd class="category-name">
					<?php echo JText::_('JCATEGORY') . ': ' . $this->category->title; ?>
				</dd>
			<?php endif;

			if (in_array('serie:speaker', $this->col_serie) and $this->item->speakers) : ?>
				<dd class="createdby">
					<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS') . ': ' . $this->item->speakers; ?>
				</dd>
			<?php endif;

			if (in_array('serie:hits', $this->col_serie)) : ?>
				<dd class="hits">
					<?php echo JText::_('JGLOBAL_HITS') . ': ' . $this->item->hits; ?>
				</dd>
			<?php endif;

			if (in_array('serie:download', $this->col_serie)) : ?>
				<dd class="hits">
					<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL') . ': '; ?>
					<a href="<?php echo JRoute::_('index.php?task=serie.download&id=' . $this->item->slug); ?>"
					   target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
						<img src="media/com_sermonspeaker/images/download.png"
							 alt="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>"/>
					</a></dd>
			<?php endif; ?>
		</dl>
	<?php endif;

	if (in_array('serie:description', $this->col_serie)) : ?>
		<div class="category-desc">
			<div class="ss-avatar">
				<?php if ($this->item->avatar) : ?>
					<img src="<?php echo trim($this->item->avatar, '/'); ?>">
				<?php endif; ?>
			</div>
			<?php echo JHtml::_('content.prepare', $this->item->series_description); ?>
			<div class="clear-left"></div>
		</div>
	<?php endif;

	if (in_array('serie:player', $this->columns) and count($this->items)) :
		JHtml::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true)); ?>
		<div class="ss-serie-player">
			<hr class="ss-serie-player"/>
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
			<hr class="ss-serie-player"/>
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
			<?php foreach ($this->items as $i => $item) :
				// Preparing tooltip
				$tip = array();

				if (in_array('sermons:num', $this->columns) and $item->sermon_number) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number;
				endif;

				if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00')) :
					$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL') . ': ' . JHtml::date($item->sermon_date, JText::_($this->params->get('date_format')), true);
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
</div>
