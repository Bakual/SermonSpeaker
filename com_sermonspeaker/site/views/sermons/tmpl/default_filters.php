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
<fieldset class="filters w-100 mb-2">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
        <div class="float-right">
            <label for="limit" class="element-invisible">
				<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
            </label>
			<?php echo $this->pagination->getLimitBox(); ?>
        </div>
	<?php endif; ?>
	<?php if ($this->params->get('filter_field')) : ?>
		<div class="form-row filter-select">
            <label class="filter-search-lbl element-invisible" for="filter-search">
				<?php echo Text::_('JGLOBAL_FILTER_LABEL') . '&#160;'; ?>
            </label>
            <div class="input-group mr-3">
                <input type="text" name="filter-search" id="filter-search" class="form-control"
                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="input-medium"
                       onchange="this.form.submit();" title="<?php echo Text::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"
                       placeholder="<?php echo Text::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>"/>
                <div class="input-group-append">
                   <span class="input-group-text hasTooltip btn" onclick="clear_all();" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>">
                       <span class="fa fa-times"></span>
                   </span>
                </div>
            </div>
			<?php if ($this->books) : ?>
                <?php array_unshift($this->books, array('items' => array(array('value' => 0, 'text' => Text::_('COM_SERMONSPEAKER_SELECT_BOOK'))))); ?>
                <?php $options = array('id' => 'filter_books', 'list.attr' => 'onchange="this.form.submit()"', 'list.select' => $this->state->get('scripture.book')); ?>
				<?php echo HtmlHelper::_('select.groupedlist', $this->books, 'book', $options); ?>
			<?php endif; ?>
			<select name="month" id="filter_months" class="form-control" onchange="this.form.submit()">
				<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
				<?php echo HtmlHelper::_('select.options', $this->months, 'value', 'text', $this->state->get('date.month'), true); ?>
			</select>
			<select name="year" id="filter_years" class="form-control" onchange="this.form.submit()">
				<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_YEAR_SHORT'); ?></option>
				<?php echo HtmlHelper::_('select.options', $this->years, 'year', 'year', $this->state->get('date.year'), true); ?>
			</select>
		</div>
	<?php endif; ?>
</fieldset>
