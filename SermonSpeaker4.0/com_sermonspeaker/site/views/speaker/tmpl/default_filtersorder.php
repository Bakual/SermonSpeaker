<?php $orderlist	= array(
				'sermon_title' => 'JGLOBAL_TITLE',
				'sermon_date' => 'COM_SERMONSPEAKER_FIELD_DATE_LABEL',
				'hits' => 'JGLOBAL_HITS',
				'ordering' => 'JFIELD_ORDERING_LABEL'
			); 
if ($this->params->get('filter_field')) :?>
	<fieldset class="filters">
		<legend class="hidelabeltxt">
			<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
		</legend>
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('JGLOBAL_FILTER_LABEL').'&nbsp;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state_sermons_sermons->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			<button type="button" onclick="clear_all();this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>&nbsp;
		</div>
		<div class="filter-select">
			<?php if ($this->books) : ?>
				<label class="filter-search-lbl" for="filter_books"><?php echo JText::_('COM_SERMONSPEAKER_BOOK').'&nbsp;'; ?></label>
				<select name="book" id="filter_books" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_BOOK'); ?></option>
					<?php echo JHtml::_('select.options', $this->books, 'value', 'text', $this->state_sermons->get('scripture.book'), true);?>
				</select>
			<?php endif; ?>
			<label class="filter-select-lbl" for="filter-select"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL').'&nbsp;'; ?></label>
			<select name="month" id="filter_months" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
				<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state_sermons->get('date.month'), true);?>
			</select>
			<select name="year" id="filter_years" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR_SHORT'); ?></option>
				<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state_sermons->get('date.year'), true);?>
			</select>
		</div>
		<div class="ordering-select">
			<label for="filter_order"><?php echo JText::_('JFIELD_ORDERING_LABEL').'&nbsp;'; ?></label>
			<select name="filter_order" id="filter_order" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
				<?php echo JHtml::_('select.options', $orderlist, '', '', $this->state_sermons->get('list.ordering'), true);?>
			</select>
			<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
				<?php echo JHtml::_('select.options', array('ASC'=>'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC'=>'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state_sermons->get('list.direction'), true);?>
			</select>
		</div>
<?php endif;
if ($this->params->get('show_pagination_limit')) : ?>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
			<?php echo $this->pag_sermons->getLimitBox(); ?>
		</div>
<?php endif;
if ($this->params->get('filter_field')) : ?>
	</fieldset>
<?php endif;