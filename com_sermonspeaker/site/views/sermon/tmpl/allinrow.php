<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');

$user       = JFactory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->item);
?>
<div class="ss-sermon-container<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/CreativeWork">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<h2 itemprop="name">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>"
			itemprop="url">
			<?php echo $this->item->title; ?></a>
	</h2>
	<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
		<ul class="actions">
			<li class="edit-icon">
				<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?>
			</li>
		</ul>
	<?php endif; ?>
	<?php echo $this->item->event->beforeDisplayContent; ?>
	<!-- Begin Header -->
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<?php if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
				<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?></th>
			<?php endif;

			if (in_array('sermon:notes', $this->columns) and strlen($this->item->notes) > 0) : ?>
				<th align="left" valign="bottom"> <?php echo JText::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL'); ?></th>
			<?php endif;

			if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
				<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></th>
			<?php endif;

			if (in_array('sermon:player', $this->columns)) : ?>
				<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERMON_PLAYER'); ?></th>
			<?php endif; ?>
		</tr>
		<!-- Begin Data -->
		<tr>
			<?php if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
				<td align="left" valign="top">
					<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; ');
					echo JHtml::_('content.prepare', $scriptures); ?>
				</td>
			<?php endif;

			if (in_array('sermon:notes', $this->columns) and strlen($this->item->notes) > 0) : ?>
				<td align="left" valign="top"><?php echo JHtml::_('content.prepare', $this->item->notes); ?></td>
			<?php endif;

			if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
				<td align="left"
					valign="top"><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?></td>
			<?php endif;

			if (in_array('sermon:player', $this->columns)) : ?>
				<td align="center" valign="top">
					<?php if ($player->error): ?>
						<span class="no_entries"><?php echo $player->error; ?></span>
					<?php else:
						echo $player->mspace;
						echo $player->script;
					endif;

					if ($player->toggle): ?>
                        <div class="row">
                            <div class="mx-auto btn-group">
                                <button type="button" onclick="Video()" class="btn btn-secondary" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>">
                                    <span class="fa fa-film fa-4x"></span>
                                </button>
                                <button type="button" onclick="Audio()" class="btn btn-secondary" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>">
                                    <span class="fa fa-music fa-4x"></span>
                                </button>
                            </div>
                        </div>
					<?php endif; ?>
				</td>
			<?php endif; ?>
		</tr>
	</table>
	<div style="float:left;">
		<?php if (in_array('sermon:download', $this->columns) and $this->item->audiofile) :
			echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->slug, 'audio', 0, $this->item->audiofilesize);
		endif; ?>
	</div>
	<div style="float:left;">
		<?php if (in_array('sermon:download', $this->columns) and $this->item->videofile) :
			echo SermonspeakerHelperSermonspeaker::insertdlbutton($this->item->slug, 'video', 0, $this->item->videofilesize);
		endif; ?>
	</div>
	<div style="float:right;">
		<?php if ($this->params->get('popup_player') and strlen($this->item->audiofile) > 0) :
			echo SermonspeakerHelperSermonspeaker::insertPopupButton($this->item->id, $player);
		endif; ?>
	</div>
	<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags->itemTags)) :
		$tagLayout = new JLayoutFile('joomla.content.tags');
		echo $tagLayout->render($this->item->tags->itemTags);
	endif;

	if ($this->params->get('enable_keywords')):
		$tags = SermonspeakerHelperSermonspeaker::insertSearchTags($this->item);

		if ($tags): ?>
			<div class="tag"><?php echo JText::_('COM_SERMONSPEAKER_TAGS') . ' ' . $tags; ?></div>
		<?php endif;
	endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<?php // Support for JComments
	$comments = JPATH_BASE . '/components/com_jcomments/jcomments.php';

	if ($this->params->get('enable_jcomments') && file_exists($comments)) : ?>
		<div class="jcomments">
			<?php
			require_once $comments;
			echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->title); ?>
		</div>
	<?php endif; ?>
</div>
