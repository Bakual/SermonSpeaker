<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$session	= JFactory::getSession();
?>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title">File</th>
			<th>Type</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td><?php echo $item['file']; ?></td>
			<td><?php echo $item['type']; ?></td>
			<td><a href="index.php?option=com_sermonspeaker&view=sermon&layout=edit&type=<?php echo $item['type']; ?>&file=<?php echo $item['file']; ?>" target="_parent">
				<img src="/administrator/components/com_sermonspeaker/images/add.png" title="<?php echo JText::_('COM_SERMONSPEAKER_NEW_SERMON'); ?>">
			</a></td>
			<td><a href="index.php?option=com_sermonspeaker&task=tools.delete&file=<?php echo $item['file'].'&'.$session->getName().'='.$session->getId().'&'.JUtility::getToken(); ?>=1" target="_parent">
				<img src="/administrator/components/com_sermonspeaker/images/delete.png" title="<?php echo JText::_('COM_SERMONSPEAKER_DELETE_FILE'); ?>">
			</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

