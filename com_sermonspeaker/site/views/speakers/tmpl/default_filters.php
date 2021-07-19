<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HtmlHelper::script('com_sermonspeaker/reset-filter.js', ['relative' => true]);

$orderlist = array(
		'title'    => 'COM_SERMONSPEAKER_FIELD_NAME_LABEL',
		'hits'     => 'JGLOBAL_HITS',
		'ordering' => 'JFIELD_ORDERING_LABEL',
);
?>
<?php if ($this->params->get('filter_field')) : ?>
	<div class="com-sermonspeaker__filter btn-group">
		<label class="filter-search-lbl visually-hidden" for="filter-search">
			<?php echo Text::_('JGLOBAL_FILTER_LABEL'); ?>
		</label>
		<input type="text" name="filter[search]" id="filter-search"
			   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
			   onchange="document.adminForm.submit();"
			   placeholder="<?php echo Text::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>">

		<?php if ($this->hasTags) : ?>
			<label class="filter-tag-lbl visually-hidden" for="filter-tag">
				<?php echo Text::_('JOPTION_SELECT_TAG'); ?>
			</label>
			<select name="filter[tag]" id="filter-tag" class="form-select" onchange="document.adminForm.submit();">
				<option value=""><?php echo Text::_('JOPTION_SELECT_TAG'); ?></option>
				<?php echo HTMLHelper::_('select.options', HTMLHelper::_('tag.options', array('filter.published' => array(1), 'filter.language' => $langFilter), true), 'value', 'text', $this->state->get('filter.tag')); ?>
			</select>
		<?php endif; ?>
		<button type="submit" name="filter_submit" class="btn btn-primary">
			<?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?></button>
		<button type="reset" name="filter-clear-button" class="btn btn-secondary">
			<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</div>
<?php endif; ?>

<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="com-sermonspeaker-speakers__pagination btn-group float-end">
		<label for="limit" class="visually-hidden">
			<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
<?php endif; ?>

<div class="com-sermonspeaker-sermons__ordering btn-group">
	<label for="filter_order" class="visually-hidden">
		<?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?>
	</label>
	<select name="filter_order" id="filter_order" class="form-select" onchange="document.adminForm.submit()">
		<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
		<?php echo HTMLHelper::_('select.options', $orderlist, '', '', $this->state->get('list.ordering'), true); ?>
	</select>
	<label for="filter_order_Dir" class="visually-hidden">
		<?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?>
	</label>
	<select name="filter_order_Dir" id="filter_order_Dir" class="form-select" onchange="document.adminForm.submit()">
		<option value="0"><?php echo Text::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
		<?php echo HTMLHelper::_('select.options', array('ASC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC' => 'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state->get('list.direction'), true); ?>
	</select>
</div>
