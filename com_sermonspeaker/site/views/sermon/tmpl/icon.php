<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::stylesheet('com_sermonspeaker/icon.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');

$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$config = array(
		'alt_player' => 1,
		'count' => 1,
		'type' => 'audio',
	);
$player = SermonspeakerHelperSermonspeaker::getPlayer($this->item, $config);
?>
<div class="ss-sermon-container<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/CreativeWork">
<?php
if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2 itemprop="name">
	<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug)); ?>" itemprop="url">
		<?php echo $this->item->title; ?>
	</a>
</h2>
<?php
if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?>
		</li>
	</ul>
<?php endif; ?>
<div id="sermon-infobox">
	<div id="sermon-player-container">
		<?php if (in_array('sermon:player', $this->columns)) : ?>
			<div class="ss-player">
				<?php if ($player->error): ?>
					<span class="no_entries"><?php echo $player->error; ?></span>
				<?php else:
					echo $player->mspace;
					echo $player->script;
				endif; ?>
			</div>
		<?php endif;

		if ($this->params->get('popup_player') or in_array('sermon:download', $this->columns)) : ?>
			<div class="ss-mp3-links">
			<?php if ($this->params->get('popup_player')) : ?>
				<a href="<?php echo JURI::current(); ?>" class="new-window" onclick="popup = window.open('<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug) . '&layout=popup&tmpl=component'); ?>', 'PopupPage', 'height=<?php echo $player->popup['height']; ?>,width=<?php echo $player->popup['width']; ?>,scrollbars=yes,resizable=yes'); return false">
					<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>
				</a>
			<?php endif;

			if ($this->params->get('popup_player') and in_array('sermon:download', $this->columns)) : ?>
				<br />
			<?php endif;

			if ($this->item->audiofile and in_array('sermon:download', $this->columns)) : ?>
				<span itemprop="audio" itemscope itemtype="http://schema.org/AudioObject">
					<a id="sermon_download" href="<?php echo JRoute::_('index.php?task=download&type=audio&id=' . $this->item->slug); ?>" class="download" itemprop="url">
						<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO'); ?>
					</a>
				</span>
			<?php endif; ?>
			</div>
		<?php endif;

		if (in_array('sermon:player', $this->columns) and $this->item->videofile) :
			if (!$player->error) : ?>
				<br style="clear:left;" />
			<?php endif; ?>
			<div class="ss-player-video">
				<a href="<?php echo JURI::current(); ?>" onclick="popup = window.open('<?php echo  JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug) . '&layout=popup&type=video&tmpl=component'); ?>', 'PopupPage', 'height=<?php echo $player->popup['height']; ?>,width=<?php echo $player->popup['width']; ?>,scrollbars=yes,resizable=yes'); return false">
					<img src="media/com_sermonspeaker/images/player.png">
				</a>
			</div>
		<?php endif;

		if ($this->item->videofile and in_array('sermon:download', $this->columns)) : ?>
			<div class="ss-mp3-links" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
				<a id="sermon_download" href="<?php echo JRoute::_('index.php?task=download&type=video&id=' . $this->item->slug); ?>" class="download" itemprop="url">
					<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO'); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<div class="ss-fields-container">
		<?php if (in_array('sermon:speaker', $this->columns) and $this->item->speaker_id): ?>
			<div class="ss-field field-speaker" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>" itemprop="author" itemscope itemtype="http://schema.org/Person">
				<?php echo JLayoutHelper::render('content.speaker', array('item' => $this->item, 'params' => $this->params)); ?>
			</div>
		<?php endif;

		if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
			<div class="ss-field field-bible" title="<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>">
				<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; ');
				echo JHtml::_('content.prepare', $scriptures); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="ss-fields-container">
		<?php if (in_array('sermon:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
			<div class="ss-field field-calendar" title="<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>">
				<time datetime="<?php echo JHtml::_('date', $this->item->sermon_date, 'c'); ?>" itemprop="dateCreated">
					<?php echo JHtml::Date($this->item->sermon_date, JText::_('DATE_FORMAT_LC1'), true); ?>
				</time>
			</div>
		<?php endif;

		if (in_array('sermon:length', $this->columns) and ($this->item->sermon_time != '00:00:00')) : ?>
			<div class="ss-field field-time" title="<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>">
				<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="ss-fields-container">
		<?php if (in_array('sermon:series', $this->columns) and $this->item->series_id) : ?>
			<div class="ss-field field-series" title="<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>">
				<?php
				if ($this->item->series_state) : ?>
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug)); ?>">
				<?php echo $this->escape($this->item->series_title); ?></a>
				<?php else :
					echo $this->escape($this->item->series_title);
				endif; ?>
			</div>
		<?php endif;

		if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
			<div class="ss-field field-addfile" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>">
				<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc, 1); ?>
			</div>
		<?php endif; ?>
	</div>
	<br style="clear:both" />
</div>
<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags)) :
	$tagLayout = new JLayoutFile('joomla.content.tags');
	echo $tagLayout->render($this->item->tags->itemTags);
endif;

if (in_array('sermon:notes', $this->columns) and $this->item->notes) : ?>
	<div class="ss-notes">
		<?php echo JHtml::_('content.prepare', $this->item->notes); ?>
	</div>
<?php endif;

if ($this->params->get('enable_keywords', 0)):
	$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item);

	if ($tags) : ?>
		<div class="tag"><?php echo JText::_('COM_SERMONSPEAKER_TAGS') . ' ' . $tags; ?></div>
	<?php endif;
endif;

if (in_array('sermon:hits', $this->columns) and $this->item->hits) : ?>
	<div class="hits">
		<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
		<?php echo JText::_('JGLOBAL_HITS') . ': ' . $this->item->hits; ?>
	</div>
<?php endif;

// Support for JComments
$comments = JPATH_BASE . '/components/com_jcomments/jcomments.php';

if ($this->params->get('enable_jcomments') && file_exists($comments)) : ?>
	<div class="jcomments">
		<?php
		require_once $comments;
		echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->title); ?>
	</div>
<?php endif; ?>
</div>
