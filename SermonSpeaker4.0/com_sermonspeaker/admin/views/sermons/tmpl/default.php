<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
// JHTML::_('script','system/multiselect.js',false,true);
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'sermons.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_speaker" id="filter_speaker" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_SERMONSPEAKER_SELECT_SPEAKER');?></option>
				<?php echo JHtml::_('select.options', $this->speakers, 'value', 'text', $this->state->get('filter.speaker'));?>
			</select>

			<select name="filter_series" id="filter_series" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_SERMONSPEAKER_SELECT_SERIES');?></option>
				<?php echo JHtml::_('select.options', $this->series, 'value', 'text', $this->state->get('filter.series'));?>
			</select>

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>

			<select name="filter_podcast" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_SERMONSPEAKER_SELECT_PCAST');?></option>
				<?php echo JHtml::_('select.options', array('0'=>JText::_('JUNPUBLISHED'), '1'=>JText::_('JPUBLISHED')), 'value', 'text', $this->state->get('filter.podcast'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_sermonspeaker'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'sermons.sermon_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SERIE', 'series_title', $listDirn, $listOrder); ?>
				</th>
				<th width="7%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'sermons.state', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL', 'sermons.podcast', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'sermons.ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'sermons.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_HITS', 'sermons.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'sermons.ordering');
			$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state', 'com_sermonspeaker.category.'.$item->catid) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) :
						echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'sermons.', $canCheckin);
					endif;
					if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&task=sermon.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->sermon_title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->sermon_title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo $this->escape($item->name); ?>
				</td>
				<td class="center">
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
				<td class="center">
					<?php echo $this->escape($item->series_title); ?>
				</td>
				<td class="center">
					<?php echo JHTML::Date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'sermons.', $canChange);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->podcast, $i, 'sermons.podcast_', $canChange);?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'sermons.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'sermons.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid),'sermons.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'sermons.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->hits; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						&nbsp;<a href="index.php?option=com_sermonspeaker&task=sermon.reset&id=<?php echo $item->id; ?>" title="<?php echo JText::_('JSEARCH_RESET'); ?>"><img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" alt="<?php echo JText::_('JSEARCH_RESET'); ?>" /></a>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php if ($item->language=='*'):?>
						<?php echo JText::alt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo $this->loadTemplate('batch'); ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>