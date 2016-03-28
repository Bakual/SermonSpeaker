<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * $item     object    The sermon item
 * $show     boolean   Whether to show the states or not
 */
extract($displayData);

if (!$show)
{
	return;
}
?>
<?php if (!$item->state) : ?>
	<span class="label label-warning">
		<?php echo JText::_('JUNPUBLISHED'); ?>
	</span>
<?php endif; ?>
<?php if (strtotime($item->publish_up) > strtotime(JFactory::getDate())) : ?>
	<span class="label label-warning">
		<?php echo JText::_('JNOTPUBLISHEDYET'); ?>
	</span>
<?php endif; ?>
<?php if ((strtotime($item->publish_down) < strtotime(JFactory::getDate())) && $item->publish_down != JFactory::getDbo()->getNullDate()) : ?>
	<span class="label label-warning">
		<?php echo JText::_('JEXPIRED'); ?>
	</span>
<?php endif; ?>
