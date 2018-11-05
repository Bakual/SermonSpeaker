<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<div class="filters btn-toolbar">
	<?php if ($this->params->get('filter_field')) : ?>
		<div class="btn-group input-append">
			<label class="filter-search-lbl element-invisible" for="filter-search">
				<?php echo JText::_('JGLOBAL_FILTER_LABEL') . '&#160;'; ?>
			</label>
			<input type="text" name="filter-search" id="filter-search"
				value="<?php echo $this->escape($this->state_sermons->get('filter.search')); ?>" class="input-medium"
				onchange="document.adminForm.submit();"
				title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"
				placeholder="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"/>
			<button class="btn tip hidden-phone hidden-tablet" type="button" onclick="clear_all();this.form.submit();"
				rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i>
			</button>
		</div>
		<div class="btn-group filter-select">
			<?php if ($this->books) : ?>
				<?php array_unshift($this->books, array('items' => array(array('value' => 0, 'text' => Text::_('COM_SERMONSPEAKER_SELECT_BOOK'))))); ?>
				<?php $options = array('id' => 'filter_books', 'list.attr' => 'onchange="this.form.submit()"', 'list.select' => $this->state_sermons->get('scripture.book')); ?>
				<?php echo HtmlHelper::_('select.groupedlist', $this->books, 'book', $options); ?>
			<?php endif; ?>
			<select name="month" id="filter_months" class="input-medium" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
				<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state_sermons->get('date.month'), true); ?>
			</select>
			<select name="year" id="filter_years" class="input-small" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR_SHORT'); ?></option>
				<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state_sermons->get('date.year'), true); ?>
			</select>
		</div>
	<?php endif;

	if ($this->params->get('show_pagination_limit')) : ?>
		<div class="btn-group pull-right">
			<label class="element-invisible">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pag_sermons->getLimitBox(); ?>
		</div>
	<?php endif; ?>
</div>
