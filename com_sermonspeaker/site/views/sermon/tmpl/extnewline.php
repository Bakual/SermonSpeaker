<?php
defined('_JEXEC') or die;
JHtml::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player = SermonspeakerHelperSermonspeaker::getPlayer($this->item);
?>
<div class="ss-sermon-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug)); ?>"><?php echo $this->item->title; ?></a></h2>
<?php if ($canEdit || ($canEditOwn && ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?>
		</li>
	</ul>
<?php endif; ?>
<div class="ss-sermondetail-container">
	<?php if (in_array('sermon:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHtml::Date($this->item->sermon_date, JText::_($this->params->get('date_format')), true); ?></div>
	<?php endif;
	if (in_array('sermon:scripture', $this->columns) && $this->item->scripture) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; ');
			echo JHtml::_('content.prepare', $scriptures); ?>
		</div>
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
		<div class="ss-sermondetail-text">
			<?php if ($this->item->series_state) : ?>
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug)); ?>">
			<?php echo $this->escape($this->item->series_title); ?></a>
			<?php else :
				echo $this->escape($this->item->series_title);
			endif; ?>
		</div>
	<?php endif;
	if (in_array('sermon:speaker', $this->columns) && $this->item->speaker_id) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php if ($this->item->speaker_state):
				echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->item->speaker_slug, false, $this->item->speaker_title); 
			else: 
				echo $this->item->speaker_title;
			endif; ?>
		</div>
		<?php if ($this->item->pic) : ?>
			<div class="ss-sermondetail-label"></div>
			<div class="ss-sermondetail-text"><img height="150" src="<?php echo SermonSpeakerHelperSermonSpeaker::makelink($this->item->pic); ?>"></div>
		<?php endif;
	endif;
	if (in_array('sermon:length', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?></div>
	<?php endif;
	if (in_array('sermon:hits', $this->columns)) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('JGLOBAL_HITS'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo $this->item->hits; ?></div>
	<?php endif;
	if (in_array('sermon:notes', $this->columns) && strlen($this->item->notes) > 0) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL'); ?>:</div>
		<div class="ss-sermondetail-text"><?php echo JHtml::_('content.prepare', $this->item->notes); ?></div>
	<?php endif;
	if (in_array('sermon:player', $this->columns)) : ?>
		<div class="ss-sermondetail-text ss-sermon-player">
			<?php if ($player->error): ?>
				<span class="no_entries"><?php echo $player->error; ?></span>
			<?php else:
				echo $player->mspace;
				echo $player->script;
			endif;
			if ($player->toggle): ?>
				<div class="ss-sermon-switch">
					<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
					<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
				</div>
			<?php endif; ?>
		</div>
	<?php endif;
	if ($this->params->get('popup_player') && ($player->mode)) : ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $player); ?></div>
	<?php endif;
	if (in_array('sermon:download', $this->columns) && $player->mode.'file') :
		$filesize	= $player->mode.'filesize'; ?>
		<div class="ss-sermondetail-label"></div>
		<div class="ss-sermondetail-text"><?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->slug, $player->mode, 0, $this->item->$filesize); ?></div>
	<?php endif;
	if (in_array('sermon:addfile', $this->columns) && $this->item->addfile) : ?>
		<div class="ss-sermondetail-label"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
		<div class="ss-sermondetail-text">
			<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc, 1); ?>
		</div>
	<?php endif; ?>
</div>
<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags)) :
	$tagLayout = new JLayoutFile('joomla.content.tags');
	echo $tagLayout->render($this->item->tags->itemTags);
endif;
if ($this->params->get('enable_keywords')):
	$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item); 
	if ($tags): ?>
		<div class="tag"><?php echo JText::_('COM_SERMONSPEAKER_TAGS').' '.$tags; ?></div>
	<?php endif;
endif;
// Support for JComments
$comments = JPATH_BASE.'/components/com_jcomments/jcomments.php';
if ($this->params->get('enable_jcomments') && file_exists($comments)) : ?>
	<div class="jcomments">
		<?php
		require_once($comments);
		echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->title); ?>
	</div>
<?php endif; ?>
</div>