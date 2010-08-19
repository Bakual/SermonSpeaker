<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
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
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_SERMONSPEAKER_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_speaker" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_SERMONSPEAKER_SELECT_SPEAKER');?></option>
				<?php echo JHtml::_('select.options', $this->speakers, 'id', 'name', $this->state->get('filter.speaker'), true);?>
			</select>

			<select name="filter_series" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_SERMONSPEAKER_SELECT_SERIES');?></option>
				<?php echo JHtml::_('select.options', $this->series, 'id', 'series_title', $this->state->get('filter.series'), true);?>
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
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SERIE', 'series_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'sermons.published', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'COM_SERMONSPEAKER_SERMONCAST', 'sermons.podcast', $listDirn, $listOrder); ?>
				</th>
				<th width="20%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_CATEGORY', 'category_title', $listDirn, $listOrder); ?>
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
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'sermons.ordering');
			$item->cat_link	= JRoute::_('index.php?option=com_categories&extension=com_sermonspeaker&task=edit&type=other&cid[]='. $item->catid);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&task=sermon.edit&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->sermon_title); ?></a>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo $this->escape($item->name); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->sermon_scripture); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->series_title); ?>
				</td>
				<td class="center">
					<?php echo JHTML::Date($item->sermon_date, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'sermons.', true);?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->podcast, $i, 'sermons.podcast_', true);?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order">
					<?php if ($saveOrder) :?>
						<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'sermons.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'sermons.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php endif; ?>
					<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				</td>
				<td class="center">
					<?php echo $item->hits; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>