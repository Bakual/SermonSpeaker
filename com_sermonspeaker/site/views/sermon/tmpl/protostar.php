<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->item);
?>
<div class="item-page<?php echo $this->pageclass_sfx; ?> ss-sermon-container<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<div class="btn-group pull-right">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="icon-cog"></i>
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<?php if (in_array('sermon:download', $this->columns)) :
				if ($this->item->audiofile) : ?>
					<li class="download-icon"><?php echo JHtml::_('icon.download', $this->item, $this->params, array('type' => 'audio')); ?></li>
				<?php endif;
				if ($this->item->videofile) : ?>
					<li class="download-icon"><?php echo JHtml::_('icon.download', $this->item, $this->params, array('type' => 'video')); ?></li>
				<?php endif; ?>
			<?php endif; ?>
			<li class="email-icon"><?php echo JHtml::_('icon.email', $this->item, $this->params, array('type' => 'sermon')); ?></li>
			<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
				<li class="edit-icon"><?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="page-header">
		<h2>
			<?php if ($this->item->state == 0): ?>
				<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
			<?php endif; ?>
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug)); ?>">
				<?php echo $this->escape($this->item->sermon_title); ?></a>
		</h2>
		<?php if (in_array('sermon:speaker', $this->columns) and $this->item->name) : ?>
			<small class="ss-speaker createdby">
				<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>: 
				<?php if ($this->item->speaker_state):
					echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($this->item->speaker_slug, $this->item->pic, $this->item->name);
				else :
					echo $this->item->name;
				endif; ?>
			</small>
		<?php endif; ?>
	</div>
	<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($this->item)) : ?>
		<div class="img-polaroid pull-right item-image"><img src="<?php echo $picture; ?>"></div>
	<?php endif; ?>
	<div class="article-info sermon-info muted">
		<dl class="article-info">
			<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
			<?php if (in_array('sermon:category', $this->columns) and $this->item->category_title) : ?>
				<dd>
					<div class="category-name">
						<i class="icon-folder"></i>
						<?php echo JText::_('JCATEGORY'); ?>:
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($this->item->catslug)); ?>"><?php echo $this->item->category_title; ?></a>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:series', $this->columns) and $this->item->series_title) : ?>
				<dd>
					<div class="category-name">
						<i class="icon-drawer-2"></i>
						<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
						<?php if ($this->item->series_state) : ?>
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug)); ?>">
						<?php echo $this->escape($this->item->series_title); ?></a>
						<?php else :
							echo $this->escape($this->item->series_title);
						endif; ?>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
				<dd>
					<div class="create">
						<i class="icon-calendar"></i>
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
						<?php echo JHtml::Date($this->item->sermon_date, JText::_($this->params->get('date_format')), true); ?>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:hits', $this->columns)) : ?>
				<dd>
					<div class="hits">
						<i class="icon-eye-open"></i>
						<?php echo JText::_('JGLOBAL_HITS'); ?>:
						<?php echo $this->item->hits; ?>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<i class="icon-quote"></i>
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
						<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; ');
						echo JHtml::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
					</div>
				</dd>
			<?php endif;
			if ($this->params->get('custom1') and $this->item->custom1) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
						<?php echo $this->item->custom1; ?>
					</div>
				</dd>
			<?php endif;
			if ($this->params->get('custom2') and $this->item->custom2) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
						<?php echo $this->item->custom2; ?>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:length', $this->columns) and $this->item->sermon_time != '00:00:00') : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<i class="icon-clock"></i>
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
						<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
					</div>
				</dd>
			<?php endif;
			if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
						<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
					</div>
				</dd>
			<?php endif; ?>
		</dl>
	</div>
	<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags)) :
		$tagLayout = new JLayoutFile('joomla.content.tags');
		echo $tagLayout->render($this->item->tags->itemTags); ?>
		<br />
	<?php endif;
	if (in_array('sermon:player', $this->columns)) : ?>
		<div class="ss-sermon-player">
			<?php if ($player->toggle): ?>
				<div class="btn-group">
					<img class="btn hasTip" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
					<img class="btn hasTip" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
				</div>
			<?php endif; ?>
			<?php if ($player->error): ?>
				<span class="well well-small"><?php echo $player->error; ?></span>
			<?php else:
				echo $player->mspace;
				echo $player->script;
			endif; ?>
		</div>
		<br />
	<?php endif;
	if (in_array('sermon:notes', $this->columns) and $this->item->notes) : ?>
		<div>
			<?php echo JHtml::_('content.prepare', $this->item->notes, '', 'com_sermonspeaker.notes'); ?>
		</div>
	<?php endif; ?>
</div>