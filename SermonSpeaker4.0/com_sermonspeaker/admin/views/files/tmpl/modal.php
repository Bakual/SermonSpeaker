<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$session	= JFactory::getSession();
?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<select name="type" class="inputbox" onchange="this.form.submit()">
				<option value="">- <?php echo JText::_('COM_SERMONSPEAKER_FIELD_TYPE_LABEL');?> -</option>
				<?php echo JHtml::_('select.options', array('audio'=>JText::_('COM_SERMONSPEAKER_AUDIO'), 'video'=>JText::_('COM_SERMONSPEAKER_VIDEO')), 'value', 'text', $this->state->get('filter.type'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>
	<table class="adminlist">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('COM_SERMONSPEAKER_FIELDSET_PATHS_LABEL'); ?></th>
				<th><?php echo JText::_('COM_SERMONSPEAKER_FIELD_TYPE_LABEL'); ?></th>
				<th><?php echo JText::_('JACTION_CREATE'); ?></th>
				<th><?php echo JText::_('JACTION_DELETE'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $item['file']; ?></td>
				<td class="center"><?php echo $item['type']; ?></td>
				<td class="center"><a href="index.php?option=com_sermonspeaker&view=sermon&layout=edit&type=<?php echo $item['type']; ?>&file=<?php echo $item['file']; ?>" target="_parent">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_sermonspeaker/images/add.png" title="<?php echo JText::_('COM_SERMONSPEAKER_NEW_SERMON'); ?>">
				</a></td>
				<td class="center"><a href="index.php?option=com_sermonspeaker&task=tools.delete&file=<?php echo $item['file'].'&'.$session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1" target="_parent">
					<img src="<?php echo JURI::root(); ?>administrator/components/com_sermonspeaker/images/delete.png" title="<?php echo JText::_('COM_SERMONSPEAKER_DELETE_FILE'); ?>">
				</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</form>