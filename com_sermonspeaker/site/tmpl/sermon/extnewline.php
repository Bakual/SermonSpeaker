<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper;

// HTMLHelper::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user       = Factory::getApplication()->getIdentity();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelper::getPlayer($this->item);
?>
<div class="ss-sermon-container<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/CreativeWork">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<h2 itemprop="name"><?php echo $this->item->title; ?></h2>
	<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php
	if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
		<ul class="actions">
			<li class="edit-icon">
				<?php echo HTMLHelper::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?>
			</li>
		</ul>
	<?php endif; ?>
	<?php echo $this->item->event->beforeDisplayContent; ?>
	<div class="container">
		<div class="row row-cols-2">
			<?php if (in_array('sermon:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:</div>
				<div class="col-md-8">
					<time datetime="<?php echo HTMLHelper::_('date', $this->item->sermon_date, 'c'); ?>"
						  itemprop="dateCreated">
						<?php echo HTMLHelper::date($this->item->sermon_date, Text::_($this->params->get('date_format'))); ?>
					</time>
				</div>
			<?php endif; ?>

			<?php if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:</div>
				<div class="col-md-8">
					<?php $scriptures = SermonspeakerHelper::insertScriptures($this->item->scripture, '; ');
					echo HTMLHelper::_('content.prepare', $scriptures); ?>
				</div>
			<?php endif; ?>

			<?php if (in_array('sermon:series', $this->columns) and $this->item->series_id) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:</div>
				<div class="col-md-8">
					<?php if ($this->item->series_state) : ?>
						<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSerieRoute($this->item->series_slug, $this->item->series_catid, $this->item->series_language)); ?>">
							<?php echo $this->escape($this->item->series_title); ?></a>
					<?php else :
						echo $this->escape($this->item->series_title);
					endif; ?>
				</div>
			<?php endif; ?>

			<?php if (in_array('sermon:speaker', $this->columns) and $this->item->speaker_id) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:</div>
				<div class="col-md-8" itemprop="author" itemscope itemtype="http://schema.org/Person">
					<?php $tmp = clone($this->item);
					$tmp->pic  = false;
					echo LayoutHelper::render('titles.speaker', array('item' => $tmp, 'params' => $this->params)); ?>
				</div>
				<?php if ($this->item->pic) : ?>
					<div class="col-md-4 fw-bold"></div>
					<div class="col-md-8">
						<img height="150" src="<?php echo SermonspeakerHelper::makeLink($this->item->pic); ?>">
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (in_array('sermon:length', $this->columns)) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:</div>
				<div class="col-md-8"><?php echo SermonspeakerHelper::insertTime($this->item->sermon_time); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:hits', $this->columns)) : ?>
				<div class="col-md-4 fw-bold"><?php echo Text::_('JGLOBAL_HITS'); ?>:</div>
				<div class="col-md-8">
					<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>"/>
					<?php echo $this->item->hits; ?>
				</div>
			<?php endif; ?>

			<?php if (in_array('sermon:notes', $this->columns) and strlen($this->item->notes) > 0) : ?>
				<div class="col-md-4 fw-bold ss-notes"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_NOTES_LABEL'); ?>:</div>
				<div class="col-md-8 ss-notes"><?php echo HTMLHelper::_('content.prepare', $this->item->notes); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:maintext', $this->columns) and strlen($this->item->maintext) > 0) : ?>
				<div class="col-md-4 fw-bold ss-maintext"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_MAINTEXT_LABEL'); ?>:</div>
				<div class="col-md-8 ss-maintext"><?php echo HTMLHelper::_('content.prepare', $this->item->maintext); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:player', $this->columns)) : ?>
				<div class="col-md-4"></div>
				<div class="col-md-8">
					<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->item, 'view' => 'sermon')); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->params->get('popup_player') and $player) : ?>
				<div class="col-md-4"></div>
				<div class="col-md-8"><?php echo SermonspeakerHelper::insertPopupButton($this->item->id, $player); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:download', $this->columns) and $this->item->audiofile) : ?>
				<div class="col-md-4"></div>
				<div class="col-md-8"><?php echo SermonspeakerHelper::insertdlbutton($this->item->slug, 'audio', 0, $this->item->audiofilesize); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:download', $this->columns) and $this->item->videofile) : ?>
				<div class="col-md-4"></div>
				<div class="col-md-8"><?php echo SermonspeakerHelper::insertdlbutton($this->item->slug, 'video', 0, $this->item->videofilesize); ?></div>
			<?php endif; ?>

			<?php if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
				<div class="col-md-4"><?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:</div>
				<div class="col-md-8">
					<?php echo SermonspeakerHelper::insertAddfile($this->item->addfile, $this->item->addfileDesc, 1); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags->itemTags)) : ?>
		<?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif;

	if ($this->params->get('enable_keywords')) :
		$tags = SermonspeakerHelper::insertSearchTags($this->item);

		if ($tags): ?>
			<div class="tag"><?php echo Text::_('COM_SERMONSPEAKER_TAGS') . ' ' . $tags; ?></div>
		<?php endif;
	endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<?php // Support for JComments
	$comments = JPATH_BASE . '/components/com_jcomments/jcomments.php';

	if ($this->params->get('enable_jcomments') and file_exists($comments)) : ?>
		<div class="jcomments">
			<?php
			require_once $comments;
			echo JComments::showComments($this->item->id, 'com_sermonspeaker', $this->item->title); ?>
		</div>
	<?php endif; ?>
</div>
