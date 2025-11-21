<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$orderlist = array(
		'title'              => 'JGLOBAL_TITLE',
		'series_description' => 'JGLOBAL_DESCRIPTION',
		'hits'               => 'JGLOBAL_HITS',
		'ordering'           => 'JFIELD_ORDERING_LABEL',
)
?>

<div class="com-sermonspeaker-series__ordering btn-group">
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
