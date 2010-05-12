<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title(JText::_('SERIES MANAGER'), 'series');
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
$ordering = ($this->lists['order'] == 'ordering');
$disabled = $ordering ?  '' : 'disabled="disabled"';
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
			echo $this->lists['catid'];
			echo $this->lists['state'];
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
			<th class="title" with="20%"><?php echo JHTML::_('grid.sort', 'SERIESTITLE', 'series.series_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="20%"><?php echo JHTML::_('grid.sort', 'SPEAKERNAME', 'speaker.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="20%"><?php echo JHTML::_('grid.sort', 'CATEGORY', 'series.catid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%">
				<?php echo JHTML::_('grid.sort', 'ORDER', 'ordering', $this->lists['order_Dir'], $this->lists['order']); ?>
				<?php echo JHTML::_('grid.order', $this->items); ?>
			</th>
			<th width="5%"><?php echo JHTML::_('grid.sort', 'Avatar', 'pic', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="5%"><?php echo JHTML::_('grid.sort', 'Published', 'series.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'series.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr> 
	</thead> 
	<!-- Pagination Footer -->
	<tfoot>
		<tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr>
	</tfoot>
	<tbody>
		<?php
		jimport('joomla.filter.output');
		$k = 0;
		for ($i=0, $n=count($this->items); $i < $n; $i++) {
			$row = &$this->items[$i];
			$checked = JHTML::_('grid.id', $i, $row->id );
			$published = JHTML::_('grid.published', $row, $i );
			$link = JFilterOutput::ampReplace('index.php?option='.$option.'&controller=serie&view=serie&task=edit&cid[]='.$row->id);
			?> 
			<tr class="<?php echo "row$k"; ?>"> 
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>"><?php echo $row->series_title; ?></a></td> 
				<td><?php echo $row->name; ?></td> 
				<td><?php echo $row->title; ?></td> 
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', $ordering); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>"<?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php
					if ($row->avatar == "") {
						$row->avatar = JURI::root()."components/com_sermonspeaker/images/nopict.jpg";
					}
					if (substr($row->avatar,0,7) == "http://") {
						$picture = $row->avatar;
					} else {
						$path = $row->avatar;
						if (substr($path,0,1) == "." ) { $path = substr($path,1); }
						if (substr($path,0,1) == "/" ) { $path = substr($path,1); }
						$picture = JURI::root().$path;
					} ?>
					<img src="<?php echo $picture; ?>" border="1" width="50" height="50">
				</td>
				<td align="center"><?php echo $published;?></td> 
				<td><?php echo $row->id; ?></td>
			</tr> 
			<?php 
			$k = 1 - $k;
		} ?>
	</tbody>
</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="controller" value="serie" />
	<input type="hidden" name="view" value="series" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>