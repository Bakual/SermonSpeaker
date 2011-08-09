<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div class="ss-sermon-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug)); ?>"><?php echo $this->item->sermon_title; ?></a></h2>
<!-- Begin Data -->
<dl class="article-info">
<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
<?php if (in_array('sermon:series', $this->columns) && $this->item->series_title) : ?>
	<dd class="category-name">
		<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
		<?php if ($this->item->series_state) : ?>
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug)); ?>">
		<?php echo $this->escape($this->item->series_title); ?></a>
		<?php else :
			echo $this->escape($this->item->series_title);
		endif; ?>
	</dd>
<?php endif;
if (in_array('sermon:date', $this->columns)) : ?>
	<dd class="create">
		<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
		<?php echo JHTML::Date($this->item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?>
	</dd>
<?php endif;
if (in_array('sermon:speaker', $this->columns) && $this->item->speaker_id) : ?>
	<dd class="createdby">
		<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
		<?php if ($this->item->speaker_state):
			echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->item->speaker_slug, $this->item->speaker_pic, $this->item->speaker_name);
		else: 
			echo $this->item->speaker_name;
		endif; ?>
	</dd>
<?php endif;	
if (in_array('sermon:hits', $this->columns)) : ?>
	<dd class="hits">
		<?php echo JText::_('JGLOBAL_HITS'); ?>: 
		<?php echo $this->item->hits; ?>
	</dd>
<?php endif;if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
	<dd class="ss-sermondetail-info">
		<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
		<?php echo JHTML::_('content.prepare', $this->item->sermon_scripture); ?>
	</dd>
<?php endif;
if ($this->params->get('custom1') && $this->item->custom1) : ?>
	<dd class="ss-sermondetail-info">
		<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
		<?php echo $this->item->custom1; ?>
	</dd>
<?php endif;
if ($this->params->get('custom2') && $this->item->custom2) : ?>
	<dd class="ss-sermondetail-info">
		<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
		<?php echo $this->item->custom2; ?>
	</dd>
<?php endif;
if (in_array('sermon:length', $this->columns)) : ?>
	<dd class="ss-sermondetail-info">
		<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
		<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
	</dd>
<?php endif;
if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
	<dd class="ss-sermondetail-info">
		<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
		<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
	</dd>
<?php endif;	
if ($this->params->get('enable_keywords')):
	$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item->metakey); 
	if ($tags): ?>
		<dd class="tag">
			<?php echo JText::_('COM_SERMONSPEAKER_TAGS').' '.$tags; ?>
		</dd>
	<?php endif;
endif; ?>
</dl>
<div class="ss-sermondetail-container">
	<?php if ($this->item->picture): ?>
		<img src="<?php echo SermonSpeakerHelperSermonSpeaker::makelink($this->item->picture); ?>" alt="" />
	<?php endif; ?>
	<?php if (in_array('sermon:player', $this->columns)) : ?>
		<div class="ss-sermon-player">
			<?php if ($this->player['status'] == 'error'): ?>
				<span class="no_entries"><?php echo $this->player['error']; ?></span>
			<?php else:
				echo $this->player['mspace'];
				echo $this->player['script'];
			endif; ?>
		</div>
	<?php endif;
	if ($this->params->get('dl_button') && ($this->player['status'] != 'error')) : ?>
		<span><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->slug, $this->item->audiofile); ?></span>
	<?php endif;
	if ($this->params->get('popup_player') && $this->player['file']) : ?>
		<span><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $this->player); ?></span>
	<?php endif;
	if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
		<div>
			<?php echo JHTML::_('content.prepare', $this->item->notes); ?>
		</div>
	<?php endif; ?>
</div>
<?php
// Support for JComments
$comments = JPATH_BASE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php';
if ($this->params->get('enable_jcomments') && file_exists($comments)) : ?>
	<div class="jcomments">
		<?php
		require_once($comments);
		echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->sermon_title); ?>
	</div>
<?php endif; ?>
</div>