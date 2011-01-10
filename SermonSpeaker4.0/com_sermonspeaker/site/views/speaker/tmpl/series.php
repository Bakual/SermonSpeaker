<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

// TODO show category name in header
$this->cat = '';

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div id="ss-speaker-container" >
<h1 class="componentheading"><?php echo $this->speaker->name.": ".JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'); ?></h1>

<?php if($this->speaker->pic) : ?>
	<a href="<?php echo JRoute::_('index.php?view=speaker&id='.$this->speaker->slug); ?>">
		<img class="speaker" src="<?php echo $this->speaker->pic; ?>" title="<?php echo $this->speaker->name; ?>" alt="<?php echo $this->speaker->name; ?>" />
	</a>
<?php endif;
if ($this->speaker->bio || ($this->speaker->intro && $this->params->get('speaker_intro'))) : ?>
	<h3 class="contentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
	<?php
	echo $this->speaker->intro;
	echo $this->speaker->bio; ?>
	</p>
<?php endif;
if ($this->speaker->website && $this->speaker->website != "http://") : ?>
	<a href="<?php echo $this->speaker->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->speaker->name); ?></a>
<?php endif; ?>
<br style="clear:both" />
<!-- Begin Data - Sermons -->
<?php if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php else : ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</fieldset>

	<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
	<!-- Tabellenkopf mit Sortierlinks erstellen -->
		<thead><tr>
			<?php if ($this->av > 0) : ?>
				<th width='10'> </th>
			<?php endif; ?>
			<th class="ss-title">
				<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'series_title', $listDirn, $listOrder); ?>
			</th>
			<th class="ss-col">
				<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERIESDESCRIPTION', 'series_description', $listDirn, $listOrder); ?>
			</th>
		</tr><thead>
	<!-- Begin Data -->
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<tr class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
					<?php if ($this->av) :
						if ($item->avatar) : ?>
							<td class="ss-col"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>"></td>
						<?php else : ?>
							<td class="ss-col"></td>
						<?php endif;
					endif; ?>
					<td class="ss-title"><a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_("index.php?view=serie&id=$item->slug" ); ?>"><?php echo $item->series_title; ?></a></td>
					<td  class="ss-col"><?php echo $item->series_description; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="pagination">
		<p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</div>
</form>
<?php endif; ?>
</div>