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
<div class="ss-sermondetail-container">
	<?php if (in_array('sermon:date', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHTML::Date($this->item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?></div>
	<?php endif;
	if (in_array('sermon:scripture', $this->columns) && $this->item->sermon_scripture) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHTML::_('content.prepare', $this->item->sermon_scripture); ?></div>
	<?php endif;
	if ($this->params->get('custom1') && $this->item->custom1) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->custom1; ?></div>
	<?php endif;
	if ($this->params->get('custom2') && $this->item->custom2) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->custom2; ?></div>
	<?php endif;
	if (in_array('sermon:series', $this->columns) && $this->item->series_id) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:</div>
		<div class="ss-sermondetail-text"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->serie->slug)); ?>">
			<?php echo $this->escape($this->serie->series_title); ?></a>
		</div>
	<?php endif;
	if (in_array('sermon:speaker', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->speaker->id, $this->speaker->pic, $this->speaker->name); ?>
		</div>
		<?php if ($this->speaker->pic) : ?>
			<div class="ss-sermondetail-label"></div>
			<div class="ss-sermondetail-text"><img height="150" src="<?php echo SermonSpeakerHelperSermonSpeaker::makelink($this->speaker->pic); ?>"></div>
		<?php endif;
	endif;
	if (in_array('sermon:length', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?></div>
	<?php endif;
	if (in_array('sermon:hits', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('JGLOBAL_HITS'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->hits; ?></div>
	<?php endif;
	if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHTML::_('content.prepare', $this->item->notes); ?></div>
	<?php endif;
	if (in_array('sermon:player', $this->columns)) : ?>
		<div class="ss-sermondetail-text ss-sermon-player">
			<?php if ($this->player['status'] == 'error'): ?>
				<span class="no_entries"><?php echo $this->player['error']; ?></span>
			<?php else:
				echo $this->player['mspace'];
				echo $this->player['script'];
			endif; ?>
		</div>
	<?php endif;
	if ($this->params->get('popup_player') && ($this->player['status'] != 'error')) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $this->player); ?></div>
	<?php endif;
	if ($this->params->get('dl_button') && $this->player['file']) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->id, $this->player['file']); ?></div>
	<?php endif;
	if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
		</div>
	<?php endif; ?>
</div>
<?php
if ($this->params->get('enable_keywords')):
	$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item->metakey); 
	if ($tags): ?>
		<div class="tag"><?php echo JText::_('COM_SERMONSPEAKER_TAGS').' '.$tags; ?></div>
	<?php endif;
endif;
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