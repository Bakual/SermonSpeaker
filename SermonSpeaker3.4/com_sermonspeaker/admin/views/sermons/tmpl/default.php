<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title(JText::_('SERMONS MANAGER'), 'sermons');
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolbarHelper::spacer();
JToolbarHelper::divider();
JToolbarHelper::spacer();
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();
JToolbarHelper::spacer();
JToolbarHelper::divider();
JToolbarHelper::spacer();
JToolBarHelper::preferences('com_sermonspeaker',550);
?>

<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'FILTER' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php
			echo $this->lists['series'];
			echo $this->lists['catid'];
			echo $this->lists['state'];
			echo $this->lists['pcast'];
			?>
		</td>
	</tr>
</table>
<div id="tablecell">
<table class="adminlist">
	<thead> 
		<tr> 
			<th width="5"><?php echo JText::_( 'NUM' ); ?></th>
			<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>			
			<th class="title"><?php echo JHTML::_('grid.sort', 'SERMONTITLE', 'sermons.sermon_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'SPEAKERNAME', 'speaker.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'SCRIPTURE', 'sermons.sermon_scripture', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'SERIES', 'series.series_title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'SERMON_DATE', 'sermons.sermon_date', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', 'CATEGORY', 'series.catid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'Published', 'sermons.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'SERMONCAST', 'sermons.podcast', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'sermons.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr> 
	</thead> 
	<!-- Pagination Footer -->
	<tfoot>
		<tr><td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td></tr>
	</tfoot>
	<tbody>
		<?php
		jimport('joomla.filter.output');
		$k = 0;
		for ($i=0, $n=count($this->items); $i < $n; $i++) {
			$row = &$this->items[$i];
			$checked = JHTML::_('grid.id', $i, $row->id);
			$published = JHTML::_('grid.published', $row, $i);
			$link = JFilterOutput::ampReplace('index.php?option='.$option.'&controller=sermon&task=edit&cid[]='.$row->id);
			?> 
			<tr class="<?php echo "row$k"; ?>"> 
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>"><?php echo $row->sermon_title; ?></a></td>
				<td><?php echo $row->name; ?></td> 
				<td><?php echo $row->sermon_scripture; ?></td>
				<td><?php echo $row->series_title; ?></td>
				<td><?php echo JHTML::date($row->sermon_date,'%x', 0); ?></td>
				<td><?php echo $row->title; ?></td> 
				<td align="center"><?php echo $published;?></td>
				<td align="center"><?php
					if($row->podcast == 1) {
						echo "<a href=\"index.php?option=com_sermonspeaker&controller=sermon&task=unpodcast&cid[]=".$row->id."\">".JHTML::_('image.administrator', 'tick.png')."</a>";
					} else {
						echo "<a href=\"index.php?option=com_sermonspeaker&controller=sermon&task=podcast&cid[]=".$row->id."\">".JHTML::_('image.administrator', 'publish_x.png')."</a>";
					} ?>
				</td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		} ?>
	</tbody>
</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="controller" value="sermon" />
	<input type="hidden" name="view" value="sermons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>