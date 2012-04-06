<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_SERMONSPEAKER_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_SERMONSPEAKER_BATCH_TIP'); ?></p>
	<?php // echo JHtml::_('batch.access');?>
	<?php // echo JHtml::_('batch.language');?>
	<?php // echo JHtml::_('batch.user', false); ?>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_sermonspeaker');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('sermon.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
