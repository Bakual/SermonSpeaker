<?php
defined('_JEXEC') or die;
JHTML::stylesheet('media/com_sermonspeaker/css/tiles.css');
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$limit 		= (int)$this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->items);
$orderlist	= array(
				'sermon_title' => 'JGLOBAL_TITLE',
				'sermon_date' => 'COM_SERMONSPEAKER_FIELD_DATE_LABEL',
				'hits' => 'JGLOBAL_HITS',
				'ordering' => 'JFIELD_ORDERING_LABEL'
			); 
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading'));
		if ($this->params->get('show_category_title')) : ?>
			<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
<?php endif;
if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif;
		if ($this->params->get('show_description') && $this->category->description) :
			echo JHtml::_('content.prepare', $this->category->description);
		endif; ?>
		<div class="clr"></div>
	</div>
<?php endif;
if (in_array('sermons:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-sermons-player">
		<hr class="ss-sermons-player" />
		<?php if ($player->player != 'PixelOut'): ?>
			<div id="playing">
				<img id="playing-pic" class="picture" src="" />
				<span id="playing-duration" class="duration"></span>
				<div class="text">
					<span id="playing-title" class="title"></span>
					<span id="playing-desc" class="desc"></span>
				</div>
				<span id="playing-error" class="error"></span>
			</div>
		<?php endif;
		echo $player->mspace;
		echo $player->script;
		?>
		<hr class="ss-sermons-player" />
	<?php if ($player->toggle): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('filter_field')) :?>
		<fieldset class="filters">
			<legend class="hidelabeltxt">
				<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
			</legend>
			<div class="filter-search">
				<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('JGLOBAL_FILTER_LABEL').'&nbsp;'; ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="filter-select">
				<label class="filter-search-lbl" for="filter_books"><?php echo JText::_('COM_SERMONSPEAKER_BOOK').'&nbsp;'; ?></label>
				<select name="book" id="filter_books" class="inputbox" onchange="this.form.submit()">
					<?php echo JHtml::_('select.options', $this->books, 'value', 'text', $this->state->get('scripture.book'), true);?>
				</select>
				<label class="filter-select-lbl" for="filter-select"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL').'&nbsp;'; ?></label>
				<select name="month" id="filter_months" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
					<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state->get('date.month'), true);?>
				</select>
				<select name="year" id="filter_years" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR'); ?></option>
					<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state->get('date.year'), true);?>
				</select>
			</div>
			<div class="ordering-select">
				<label for="filter_order"><?php echo JText::_('JFIELD_ORDERING_LABEL').'&nbsp;'; ?></label>
				<select name="filter_order" id="filter_order" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDERING'); ?></option>
					<?php echo JHtml::_('select.options', $orderlist, '', '', $this->state->get('list.ordering'), true);?>
				</select>
				<select name="filter_order_Dir" id="filter_order_Dir" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_ORDER_DIR'); ?></option>
					<?php echo JHtml::_('select.options', array('ASC'=>'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_ASC', 'DESC'=>'COM_SERMONSPEAKER_SELECT_ORDER_DIR_OPTION_DESC'), '', '', $this->state->get('list.direction'), true);?>
				</select>
			</div>
	<?php endif;
	if ($this->params->get('show_pagination_limit')) : ?>
			<div class="display-limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
	<?php endif;
	if ($this->params->get('filter_field')) : ?>
		</fieldset>
	<?php endif;
	if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<?php foreach($this->items as $i => $item) :
			// Preparing tooltip
			$tip = array();
			if(in_array('sermons:num', $this->columns) and $item->sermon_number):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL').': '.$item->sermon_number; 
			endif;
			if(in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00')):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL').': '.JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), true); 
			endif;
			if(in_array('sermons:speaker', $this->columns) and $item->name):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL').': '.$item->name;
			endif;
			if(in_array('sermons:series', $this->columns) and $item->series_title):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL').': '.$item->series_title;
			endif;
			if(in_array('sermons:scripture', $this->columns) and $item->scripture):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL').': '.SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false);
			endif;
			if(in_array('sermons:length', $this->columns) and $item->sermon_time):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL').': '.SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time);
			endif;
			if(in_array('sermons:hits', $this->columns) and $item->hits):
				$tip[]	= JText::_('JGLOBAL_HITS').': '.$item->hits;
			endif;
			if(in_array('sermons:notes', $this->columns) and $item->notes):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL').': '.$item->notes;
			endif;
			$tooltip	= implode('<br/>', $tip);
			$picture = SermonspeakerHelperSermonspeaker::insertPicture($item);
			if (!$picture): 
				$picture = 'media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
			endif; ?>
			<div id="sermon<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTip" title="<?php echo $this->escape($item->sermon_title).'::'.$this->escape($tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->id));?>">
					<img border="0" align="middle" src="<?php echo $picture; ?>">
					<span class="item-title">
						<?php echo $item->sermon_title; ?>
					</span>
				</a>
				</span>
			</div>
		<?php endforeach; ?>
		<br class="clear-left" />
	<?php endif;
	if ($this->params->get('show_pagination') && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
</form>
<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
	<div class="cat-children">
		<h3>
			<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
		</h3>
		<?php echo $this->loadTemplate('children'); ?>
	</div>
<?php endif; ?>
</div>