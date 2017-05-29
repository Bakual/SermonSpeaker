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
	'title'    => 'COM_SERMONSPEAKER_FIELD_NAME_LABEL',
	'hits'     => 'JGLOBAL_HITS',
	'ordering' => 'JFIELD_ORDERING_LABEL',
);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-speakers-container<?php echo $this->pageclass_sfx; ?>">
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

	if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;

			if ($this->params->get('show_description') && $this->category->description) :
				echo JHtml::_('content.prepare', $this->category->description);
			endif; ?>
			<div class="clr"></div>
		</div>
	<?php endif; ?>
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
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox"
					onchange="document.adminForm.submit();"
					title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"/>
			</div>
			<div class="ordering-select">
				<label for="filter_order"><?php echo JText::_('JFIELD_ORDERING_LABEL') . '&nbsp;'; ?></label>
				<select name="filter_order" id="filter_order" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
					<?php echo JHtml::_('select.options', $orderlist, '', '', $this->state->get('list.ordering'), true); ?>
				</select>
				<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
					<?php echo JHtml::_('select.options', array('ASC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state->get('list.direction'), true); ?>
				</select>
			</div>
			<?php endif;

			if ($this->params->get('show_pagination_limit')) : ?>
				<div class="display-limit">
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif;

			if ($this->params->get('filter_field')) : ?>
		</fieldset>
	<?php endif;

	if (!count($this->items)) : ?>
		<div
			class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
	<?php else : ?>
		<?php foreach ($this->items as $i => $item) :
			// Preparing tooltip
			$tip = array();

			if (in_array('speakers:category', $this->col_speaker)) :
				$tip[] = JText::_('JCATEGORY') . ': ' . $item->category_title;
			endif;

			if (in_array('speakers:hits', $this->col_speaker) and $item->hits) :
				$tip[] = JText::_('JGLOBAL_HITS') . ': ' . $item->hits;
			endif;

			if (in_array('speakers:intro', $this->col_speaker) and $item->intro) :
				$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_INTRO_LABEL') . ': ' . JHtml::_('content.prepare', $item->intro);
			endif;

			if (in_array('speakers:bio', $this->col_speaker) and $item->bio) :
				$tip[] = JText::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL') . ': ' . JHtml::_('content.prepare', $item->bio);
			endif;
			$tooltip = implode('<br/>', $tip);
			$image   = ($item->pic) ? $item->pic : 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
			<div id="serie<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTooltip"
					title="<?php echo JHtml::tooltipText($item->title, $tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
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
