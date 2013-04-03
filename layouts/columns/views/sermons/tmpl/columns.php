<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::stylesheet('com_sermonspeaker/columns.css', '', true);
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$limit 		= (int)$this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->items);
$version	= new JVersion;
$j30		= ($version->isCompatible(3.0)) ? '30' : '';
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
if (in_array('sermons:player', $this->columns) and count($this->items)) :
	JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
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
			<img class="pointer btn" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer btn" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
	<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
		echo $this->loadTemplate('filtersorder'.$j30);
	endif;
	if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<hr class="ss-sermons-player" style="clear:both" />
		<?php foreach ($this->items as $i => $item) : ?>
			<div id="sermon<?php echo $i; ?>" class="ss-entry">
				<div class="column-picture" onclick="ss_play('<?php echo $i; ?>')">
					<div class="ss-picture">
						<?php $picture = SermonspeakerHelperSermonspeaker::insertPicture($item);
						if (!$picture): 
							$picture = 'media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
						endif; ?>
						<img src="<?php echo $picture; ?>">
					</div>
				</div>
				<div class="column-content" onclick="ss_play('<?php echo $i; ?>')">
					<h3 class="title"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?>"><?php echo $item->sermon_title; ?></a>
						<?php if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) :
							echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon'));
						endif; ?>
					</h3>
					<?php $class = '';
					if (in_array('sermons:scripture', $this->columns) && $item->scripture) :
						$class = 'scripture'; ?>
						<span class="scripture">
							<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
							echo JHtml::_('content.prepare', $scriptures); ?>
						</span>
					<?php endif;
					if (in_array('sermons:speaker', $this->columns) && $item->name) : ?>
						<span class="speaker <?php echo $class; ?>">
							<?php if ($item->speaker_state):
								echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
							else: 
								echo $item->name;
							endif; ?>
						</span>
					<?php endif;
					if (in_array('sermons:series', $this->columns) && $item->series_title) : ?>
						<br />
						<?php if ($item->series_state) : ?>
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
						<?php echo $this->escape($item->series_title); ?></a>
						<?php else :
							echo $this->escape($item->series_title);
						endif;
					endif;
					if (in_array('sermons:notes', $this->columns) && $item->notes) : ?>
						<div>
							<?php echo JHtml::_('content.prepare', $item->notes); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="column-files">
					<?php if (in_array('sermons:addfile', $this->columns) && $item->addfile) :
						$link = SermonspeakerHelperSermonspeaker::makelink($item->addfile);
						// Get extension of file
						jimport('joomla.filesystem.file');
						$ext = JFile::getExt($item->addfile);
						if (file_exists(JPATH_SITE.'/media/com_sermonspeaker/icons'.'/'.$ext.'.png') ):
							$file = JURI::root().'media/com_sermonspeaker/icons/'.$ext.'.png';
						else :
							$file = JURI::root().'media/com_sermonspeaker/icons/icon.png';
						endif;
						// Show filename if no addfileDesc is set
						if (!$item->addfileDesc) :
							if ($default = $this->params->get('addfiledesc')) :
								$item->addfileDesc = $default;
							else :
								$slash = strrpos($item->addfile, '/');
								if ($slash !== false) :
									$item->addfileDesc = substr($item->addfile, $slash + 1);
								else :
									$item->addfileDesc = $item->addfile;
								endif;
							endif;
						endif; ?>
						<a href="<?php echo $link; ?>" class="addfile" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER'); ?>">
							<img src="<?php echo $file; ?>" alt="" /> <?php echo $item->addfileDesc; ?>
						</a>
					<?php endif;
					if (in_array('sermons:download', $this->columns)) : ?>
						<?php if ($item->audiofile) :
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'audio', 4, $item->audiofilesize);
						endif;
						if ($item->videofile) :
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'video', 4, $item->videofilesize);
						endif;
					endif;
					if ($item->audiofile) : ?>
						<a href="#" onclick="popup=window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&tmpl=component&type=audio&id='.$item->slug); ?>', 'PopupPage', 'height=150px, width=400px, scrollbars=yes, resizable=yes'); return false" class="listen" title="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
							Listen
						</a>
					<?php endif;
					if ($item->videofile) : ?>
						<a href="#" onclick="popup=window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&tmpl=component&type=video&id='.$item->slug); ?>', 'PopupPage', 'height=400px, width=450px, scrollbars=yes, resizable=yes'); return false" class="watch" title="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
							Watch
						</a>
					<?php endif; ?>
				</div>
				<div class="column-detail" onclick="ss_play('<?php echo $i; ?>')">
					<?php if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
						<div class="create">
							<?php echo JHtml::Date($item->sermon_date, JText::_('DATE_FORMAT_LC1'), true); ?>
						</div>
					<?php endif;
					if (in_array('sermons:category', $this->columns)) : ?>
						<div class="category-name">
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
						</div>
					<?php endif;
					if (in_array('sermons:hits', $this->columns)) : ?>
						<div class="hits">
							<?php echo JText::_('JGLOBAL_HITS'); ?>: 
							<?php echo $item->hits; ?>
						</div>
					<?php endif;
					if (in_array('sermons:length', $this->columns)) : ?>
						<div class="ss-sermondetail-info">
							<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
							<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
						</div>
					<?php endif;
					if ($this->params->get('custom1') && $item->custom1) : ?>
						<div class="ss-sermondetail-info">
							<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
							<?php echo $item->custom1; ?>
						</div>
					<?php endif;
					if ($this->params->get('custom2') && $item->custom2) : ?>
						<div class="ss-sermondetail-info">
							<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
							<?php echo $item->custom2; ?>
						</div>
					<?php endif; ?>
				</div>
				<br style="clear:both" />
			</div>
			<hr class="ss-sermons-player" />
		<?php endforeach;
	endif;
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