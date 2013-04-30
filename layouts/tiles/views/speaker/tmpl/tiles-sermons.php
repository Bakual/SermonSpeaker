<?php
defined('_JEXEC') or die;
JHtml::stylesheet('com_sermonspeaker/tiles.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->sermons);
$version	= new JVersion;
$j30		= ($version->isCompatible(3.0)) ? '30' : '';
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug).'&layout=sermons'); ?>"><?php echo $this->item->name; ?></a></h2>
<?php if ($canEdit || ($canEditOwn && ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?>
		</li>
	</ul>
<?php endif;
if ($this->params->get('show_category_title', 0) || in_array('speaker:hits', $this->columns)): ?>
	<dl class="article-info speaker-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php if ($this->params->get('show_category_title', 0)): ?>
		<dd class="category-name">
			<?php echo JText::_('JCATEGORY').': '.$this->category->title; ?>
		</dd>
	<?php endif;
	if (in_array('speaker:hits', $this->columns)): ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS').': '.$this->item->hits; ?>
		</dd>
	<?php endif; ?>
	</dl>
<?php endif; ?>
<div class="category-desc">
	<div class="ss-pic">
		<?php if ($this->item->pic) : ?>
			<img src="<?php echo trim($this->item->pic, '/'); ?>" title="<?php echo $this->item->name; ?>" alt="<?php echo $this->item->name; ?>" />
		<?php endif; ?>
	</div>
	<?php if (($this->item->bio && in_array('speaker:bio', $this->columns)) || ($this->item->intro && in_array('speaker:intro', $this->columns))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		if (in_array('speaker:intro', $this->columns)):
			echo JHtml::_('content.prepare', $this->item->intro);
		endif;
		if (in_array('speaker:bio', $this->columns)):
			echo JHtml::_('content.prepare', $this->item->bio);
		endif;
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->item->website && $this->item->website != 'http://') : ?>
		<a href="<?php echo $this->item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->item->name); ?></a>
	<?php endif; ?>
</div>
<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) :
	JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
	<div class="ss-speaker-player">
		<hr class="ss-speaker-player" />
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
		<hr class="ss-speaker-player" />
	<?php if ($player->toggle): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
	<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
		echo $this->loadTemplate('filters'.$j30);
	endif;
	if (!count($this->sermons)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<?php foreach($this->sermons as $i => $item) :
			// Preparing tooltip
			$tip = array();
			if(in_array('speaker:num', $this->col_sermon) and $item->sermon_number):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL').': '.$item->sermon_number; 
			endif;
			if(in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00')):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL').': '.JHtml::Date($item->sermon_date, JText::_($this->params->get('date_format')), true); 
			endif;
			if(in_array('speaker:category', $this->col_sermon)):
				$tip[]	= JText::_('JCATEGORY').': '.$item->category_title;
			endif;
			if(in_array('speaker:speaker', $this->col_sermon) and $item->name):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL').': '.$item->name;
			endif;
			if(in_array('speaker:series', $this->col_sermon) and $item->series_title):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL').': '.$item->series_title;
			endif;
			if(in_array('speaker:scripture', $this->col_sermon) and $item->scripture):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL').': '.SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false);
			endif;
			if(in_array('speaker:length', $this->col_sermon) and $item->sermon_time):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL').': '.SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time);
			endif;
			if(in_array('speaker:hits', $this->col_sermon) and $item->hits):
				$tip[]	= JText::_('JGLOBAL_HITS').': '.$item->hits;
			endif;
			if(in_array('speaker:notes', $this->col_sermon) and $item->notes):
				$tip[]	= JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL').': '.$item->notes;
			endif;
			$tooltip	= implode('<br/>', $tip);
			$picture = SermonspeakerHelperSermonspeaker::insertPicture($item);
			if (!$picture): 
				$picture = 'media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
			endif; ?>
			<div id="sermon<?php echo $i; ?>" class="ss-entry tile">
				<span class="hasTip" title="<?php echo $this->escape($item->title).'::'.$this->escape($tooltip); ?>">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->id));?>">
					<img border="0" align="middle" src="<?php echo trim($picture, '/'); ?>">
					<span class="item-title">
						<?php echo $item->title; ?>
					</span>
				</a>
				</span>
			</div>
		<?php endforeach; ?>
		<br class="clear-left" />
	<?php endif;
	if ($this->params->get('show_pagination') && ($this->pag_sermons->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pag_sermons->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pag_sermons->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
</form>
</div>