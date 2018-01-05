<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('stylesheet', 'com_sermonspeaker/tiles.css', array('relative' => true));
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');
$user       = JFactory::getUser();
$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker');
$orderlist  = array(
	'title'              => 'JGLOBAL_TITLE',
	'series_description' => 'JGLOBAL_DESCRIPTION',
	'hits'               => 'JGLOBAL_HITS',
	'ordering'           => 'JFIELD_ORDERING_LABEL',
);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<h2>
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>"><?php echo $this->item->title; ?></a>
	</h2>
	<?php
	if ($canEdit || ($canEditOwn && ($user->id == $this->item->created_by))) : ?>
		<ul class="actions">
			<li class="edit-icon">
				<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?>
			</li>
		</ul>
	<?php endif;

	if ($this->params->get('show_category_title', 0) || in_array('speaker:hits', $this->columns)) : ?>
		<dl class="article-info speaker-info">
			<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
			<?php
			if ($this->params->get('show_category_title', 0)) : ?>
				<dd class="category-name">
					<?php echo JText::_('JCATEGORY') . ': ' . $this->category->title; ?>
				</dd>
			<?php endif;

			if (in_array('speaker:hits', $this->columns)) : ?>
				<dd class="hits">
					<?php echo JText::_('JGLOBAL_HITS') . ': ' . $this->item->hits; ?>
				</dd>
			<?php endif; ?>
		</dl>
	<?php endif; ?>
	<div class="category-desc">
		<div class="ss-pic">
			<?php if ($this->item->pic) : ?>
				<img src="<?php echo trim($this->item->pic, '/'); ?>" title="<?php echo $this->item->name; ?>"
					alt="<?php echo $this->item->title; ?>"/>
			<?php endif; ?>
		</div>
		<?php if (($this->item->bio && in_array('speaker:bio', $this->columns)) || ($this->item->intro && in_array('speaker:intro', $this->columns))) : ?>
			<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
			<?php
			if (in_array('speaker:intro', $this->columns)) :
				echo JHtml::_('content.prepare', $this->item->intro);
			endif;

			if (in_array('speaker:bio', $this->columns)) :
				echo JHtml::_('content.prepare', $this->item->bio);
			endif;
		endif; ?>
		<div class="clear-left"></div>
		<?php if ($this->item->website && $this->item->website != 'http://') : ?>
			<a href="<?php echo $this->item->website; ?>" target="_blank"
				title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->item->title); ?></a>
		<?php endif; ?>
	</div>
	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post"
		id="adminForm" name="adminForm">
		<?php
		if ($this->params->get('filter_field')) : ?>
		<fieldset class="filters">
			<legend class="hidelabeltxt">
				<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
			</legend>
			<div class="filter-search">
				<label class="filter-search-lbl"
					for="filter-search"><?php echo JText::_('JGLOBAL_FILTER_LABEL') . '&nbsp;'; ?></label>
				<input type="text" name="filter-search" id="filter-search"
					value="<?php echo $this->escape($this->state_series->get('filter.search')); ?>" class="inputbox"
					onchange="document.adminForm.submit();"
					title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"/>
			</div>
			<div class="ordering-select">
				<label for="filter_order"><?php echo JText::_('JFIELD_ORDERING_LABEL') . '&nbsp;'; ?></label>
				<select name="filter_order" id="filter_order" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
					<?php echo JHtml::_('select.options', $orderlist, '', '', $this->state_series->get('list.ordering'), true); ?>
				</select>
				<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
					<?php echo JHtml::_('select.options', array('ASC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state_series->get('list.direction'), true); ?>
				</select>
			</div>
			<?php endif;

			if ($this->params->get('show_pagination_limit')) : ?>
				<div class="display-limit">
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
					<?php echo $this->pag_series->getLimitBox(); ?>
				</div>
			<?php endif;

			if ($this->params->get('filter_field')) : ?>
		</fieldset>
	<?php endif;

	if (!count($this->series)) : ?>
		<div
			class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
	<?php else :
		foreach ($this->series as $i => $item) :
			// Preparing tooltip
			$tip = array();

			if (in_array('series:category', $this->col_serie)) :
				$tip[] = JText::_('JCATEGORY') . ': ' . $item->category_title;
			endif;

			if (in_array('series:speaker', $this->col_serie) and $item->names) :
				$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL') . ': ' . $item->names;
			endif;

			if (in_array('series:hits', $this->col_serie) and $item->hits) :
				$tip[] = JText::_('JGLOBAL_HITS') . ': ' . $item->hits;
			endif;

			if (in_array('series:description', $this->col_serie) and $item->series_description) :
				$tip[] = JText::_('JGLOBAL_DESCRIPTION') . ': ' . JHtml::_('content.prepare', $item->series_description);
			endif;
			$tooltip = implode('<br/>', $tip);
			$image   = ($item->avatar) ? $item->avatar : 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
			<div id="serie<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTooltip"
					title="<?php echo JHtml::tooltipText($item->title, $tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
					<img border="0" align="middle" src="<?php echo trim($image, '/'); ?>">
					<span class="item-title">
						<?php echo $item->title; ?>
					</span>
				</a>
				</span>
			</div>
		<?php endforeach; ?>
		<div class="clear-left"></div>
	<?php endif;

	if ($this->params->get('show_pagination') && ($this->pag_series->pagesTotal > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pag_series->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pag_series->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
</div>
