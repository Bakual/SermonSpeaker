<?php
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'sermons.ordering';
if ($saveOrder) :
	$saveOrderingUrl = 'index.php?option=com_sermonspeaker&task=sermons.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'sermonList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
endif;
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>

		<table class="table table-striped" id="sermonList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'sermons.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="1%" style="min-width:40px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" style="min-width:40px" class="nowrap center">
						<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL', 'sermons.podcast', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone hidden-tablet">
						<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SPEAKER', 'speakers_title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone hidden-tablet">
						<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone hidden-tablet">
						<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SERIE', 'series_title', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'JGLOBAL_HITS', 'sermons.hits', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering	= ($listOrder == 'sermons.ordering');
				$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
				$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $userId;
				$canChange	= $user->authorise('core.edit.state', 'com_sermonspeaker.category.'.$item->catid) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
					<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';

						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'sermons.', $canChange); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->podcast, $i, 'sermons.podcast_', $canChange); ?>
						</div>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'sermons.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&task=sermon.edit&id=' . $item->id);?>" title="<?php echo JText::_('JACTION_EDIT');?>">
									<?php echo $this->escape($item->title); ?></a>
							<?php else : ?>
								<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias));?>"><?php echo $this->escape($item->title); ?></span>
							<?php endif; ?>
							<div class="small">
								<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
							</div>
						</div>
						<div class="pull-left">
							<?php
								// Create dropdown items
								JHtml::_('dropdown.edit', $item->id, 'sermon.');
								JHtml::_('dropdown.divider');
								if ($item->state) :
									JHtml::_('dropdown.unpublish', 'cb' . $i, 'sermons.');
								else :
									JHtml::_('dropdown.publish', 'cb' . $i, 'sermons.');
								endif;

								JHtml::_('dropdown.divider');

								if ($this->state->get('filter.state') == 2) :
									JHtml::_('dropdown.unarchive', 'cb' . $i, 'sermons.');
								else :
									JHtml::_('dropdown.archive', 'cb' . $i, 'sermons.');
								endif;

								if ($item->checked_out) :
									JHtml::_('dropdown.checkin', 'cb' . $i, 'sermons.');
								endif;

								if ($this->state->get('filter.state') == -2) :
									JHtml::_('dropdown.untrash', 'cb' . $i, 'sermons.');
								else :
									JHtml::_('dropdown.trash', 'cb' . $i, 'sermons.');
								endif;

								// Render dropdown list
								echo JHtml::_('dropdown.render');
								?>
						</div>
					</td>
					<td class="nowrap small hidden-phone hidden-tablet">
						<?php echo $this->escape($item->speakers_title); ?>
					</td>
					<td class="center small hidden-phone hidden-tablet">
						<?php if ($item->scripture):
							$passages	= explode('!', $item->scripture);
							$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
							$j = 1;
							foreach ($passages as $passage){
								$explode	= explode('|',$passage);
								if ($explode[5]){
									if ($explode[0]){
										echo $explode[5];
									} else {
										echo '<i><u>'.$explode[5].'</u></i>';
									}
								} else {
									echo JText::_('COM_SERMONSPEAKER_BOOK_'.$explode[0]);
									if ($explode[1]){
										echo '&nbsp;'.$explode[1];
										if ($explode[2]){
											echo $separator.$explode[2];
										}
										if ($explode[3] || $explode[4]){
											echo '-';
											if ($explode[3]){
												echo $explode[3];
												if ($explode[4]){
													echo $separator.$explode[4];
												}
											} else {
												echo $explode[4];
											}
										}
									}
								}
								if($j < count($passages)){
									echo '<br/ >';
								}
								$j++;
							}
						endif; ?>
					</td>
					<td class="small hidden-phone hidden-tablet">
						<?php echo $this->escape($item->series_title); ?>
					</td>
					<td class="nowrap small hidden-phone">
						<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
							echo JHtml::Date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true);
						endif; ?>
					</td>
					<td class="center small hidden-phone">
						<?php echo $item->hits; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							&nbsp;<a href="index.php?option=com_sermonspeaker&task=sermon.reset&id=<?php echo $item->id; ?>"><i class="icon-loop" rel="tooltip" title="<?php echo JText::_('JSEARCH_RESET'); ?>"> </i></a>
						<?php endif; ?>
					</td>
					<td class="small hidden-phone">
						<?php if ($item->language == '*'):?>
							<?php echo JText::alt('JALL', 'language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif;?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php echo $this->pagination->getListFooter(); ?>
		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>