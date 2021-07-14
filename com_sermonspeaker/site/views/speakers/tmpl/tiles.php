<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('stylesheet', 'com_sermonspeaker/tiles.css', array('relative' => true));
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
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
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post"
		id="adminForm" name="adminForm">
		<?php
		if ($this->params->get('filter_field')) : ?>
		<fieldset class="filters">
			<legend class="hidelabeltxt">
				<?php echo Text::_('JGLOBAL_FILTER_LABEL'); ?>
			</legend>
			<div class="filter-search">
				<label class="filter-search-lbl"
					for="filter-search"><?php echo Text::_('JGLOBAL_FILTER_LABEL') . '&nbsp;'; ?></label>
				<input type="text" name="filter-search" id="filter-search"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox"
					onchange="document.adminForm.submit();"
					title="<?php echo Text::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"/>
			</div>
			<div class="ordering-select">
				<label for="filter_order"><?php echo Text::_('JFIELD_ORDERING_LABEL') . '&nbsp;'; ?></label>
				<select name="filter_order" id="filter_order" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
					<?php echo HTMLHelper::_('select.options', $orderlist, '', '', $this->state->get('list.ordering'), true); ?>
				</select>
				<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
					<?php echo HTMLHelper::_('select.options', array('ASC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state->get('list.direction'), true); ?>
				</select>
			</div>
			<?php endif;

			if ($this->params->get('show_pagination_limit')) : ?>
				<div class="display-limit">
					<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif;

			if ($this->params->get('filter_field')) : ?>
		</fieldset>
	<?php endif;

	if (!count($this->items)) : ?>
		<div class="alert alert-info">
			<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?>
		</div>
	<?php else : ?>
		<?php foreach ($this->items as $i => $item) :
			// Preparing tooltip
			$tip = array();

			if (in_array('speakers:category', $this->col_speaker)) :
				$tip[] = Text::_('JCATEGORY') . ': ' . $item->category_title;
			endif;

			if (in_array('speakers:hits', $this->col_speaker) and $item->hits) :
				$tip[] = Text::_('JGLOBAL_HITS') . ': ' . $item->hits;
			endif;

			if (in_array('speakers:intro', $this->col_speaker) and $item->intro) :
				$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_INTRO_LABEL') . ': ' . HTMLHelper::_('content.prepare', $item->intro);
			endif;

			if (in_array('speakers:bio', $this->col_speaker) and $item->bio) :
				$tip[] = Text::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL') . ': ' . HTMLHelper::_('content.prepare', $item->bio);
			endif;
			$tooltip = implode('<br/>', $tip);
			$image   = ($item->pic) ? $item->pic : 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
			<div id="serie<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTooltip"
					title="<?php echo HTMLHelper::tooltipText($item->title, $tooltip); ?>">
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
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3>
				<?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?>
			</h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
