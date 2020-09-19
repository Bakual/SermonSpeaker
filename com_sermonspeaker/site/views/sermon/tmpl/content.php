<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');
HTMLHelper::_('bootstrap.framework');
HTMLHelper::_('bootstrap.tooltip');

// Needed for pictures in blog layout
HTMLHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->item);
?>
<div
	class="item-page<?php echo $this->pageclass_sfx; ?> ss-sermon-container<?php echo $this->pageclass_sfx; ?> clearfix"
	itemscope itemtype="http://schema.org/CreativeWork">
	<?php
	if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<div class="page-header">
		<h2 itemprop="name">
			<?php echo LayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => $showState)); ?>
			<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>"
				itemprop="url">
				<?php echo $this->escape($this->item->title); ?></a>
		</h2>
		<?php if (in_array('sermon:speaker', $this->columns) and $this->item->speaker_title) : ?>
			<small class="ss-speaker createdby" itemprop="author" itemscope itemtype="http://schema.org/Person">
				<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
				<?php echo LayoutHelper::render('titles.speaker', array('item' => $this->item, 'params' => $this->params)); ?>
			</small>
		<?php endif; ?>
	</div>
	<div class="btn-group pull-right">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			<span class="icon-cog"></span>
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
            <?php if ($this->params->get('popup_player') and $player) : ?>
				<?php if ($this->item->audiofile) : ?>
					<li class="download-icon" itemprop="audio" itemscope itemtype="http://schema.org/AudioObject">
                        <a href="#"
                           onclick="popup=window.open(
                               '<?php echo JRoute::_('index.php?view=sermon&layout=popup&id=' . $this->item->id . '&tmpl=component'); ?>',
                               'PopupPage',
                               'height=<?php echo $player->popup['height']; ?>',
                               'width=<?php echo $player->popup['width']; ?>',
                               'scrollbars=yes',
                               'resizable=yes'
                           ); return false"
                        >
                            <?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>
                        </a>
					</li>
				<?php endif; ?>
            <?php endif; ?>
			<?php if (in_array('sermon:download', $this->columns)) :
				if ($this->item->audiofile) : ?>
					<li class="download-icon" itemprop="audio" itemscope itemtype="http://schema.org/AudioObject">
						<?php echo HTMLHelper::_('icon.download', $this->item, $this->params, array('type' => 'audio')); ?>
					</li>
				<?php endif;

				if ($this->item->videofile) : ?>
					<li class="download-icon" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
						<?php echo HTMLHelper::_('icon.download', $this->item, $this->params, array('type' => 'video')); ?>
					</li>
				<?php endif; ?>
			<?php endif; ?>
			<?php
			if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
				<li class="edit-icon"><?php echo HTMLHelper::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?></li>
			<?php endif; ?>
		</ul>
	</div>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<div class="article-info sermon-info muted">
		<dl class="article-info">
			<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
			<?php
			if (in_array('sermon:category', $this->columns) and $this->item->category_title) : ?>
				<dd class="category-name">
					<?php echo Text::_('JCATEGORY'); ?>:
					<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($this->item->catid, $this->item->language)); ?>"
						itemprop="genre">
						<?php echo $this->item->category_title; ?>
					</a>
				</dd>
			<?php endif;

			if (in_array('sermon:series', $this->columns) and $this->item->series_title) : ?>
				<dd class="ss-sermondetail-info">
					<span class="icon-drawer-2"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
					<?php
					if ($this->item->series_state) : ?>
						<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug, $this->item->series_catid, $this->item->series_language)); ?>">
							<?php echo $this->escape($this->item->series_title); ?></a>
					<?php else :
						echo $this->escape($this->item->series_title);
					endif; ?>
				</dd>
			<?php endif;

			if (in_array('sermon:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
				<dd class="create">
					<span class="icon-calendar"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
					<time datetime="<?php echo HTMLHelper::_('date', $this->item->sermon_date, 'c'); ?>"
						itemprop="dateCreated">
						<?php echo HTMLHelper::date($this->item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
					</time>
				</dd>
			<?php endif;

			if (in_array('sermon:hits', $this->columns)) : ?>
				<dd class="hits">
					<span class="icon-eye-open"></span>
					<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>"/>
					<?php echo Text::_('JGLOBAL_HITS'); ?>:
					<?php echo $this->item->hits; ?>
				</dd>
			<?php endif;

			if (in_array('sermon:scripture', $this->columns) and $this->item->scripture) : ?>
				<dd class="ss-sermondetail-info">
					<span class="icon-quote"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
					<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; ');
					echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
				</dd>
			<?php endif;

			if (in_array('sermon:length', $this->columns) and $this->item->sermon_time != '00:00:00') : ?>
				<dd class="ss-sermondetail-info">
					<i class="icon-clock"></i>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
				</dd>
			<?php endif;

			if (in_array('sermon:addfile', $this->columns) and $this->item->addfile) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
				</dd>
			<?php endif; ?>
		</dl>
	</div>
	<?php echo $this->item->event->beforeDisplayContent; ?>
	<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($this->item)) : ?>
		<div class="img-polaroid pull-right item-image sermon-image"><img src="<?php echo $picture; ?>"></div>
	<?php endif; ?>

	<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags->itemTags)) : ?>
		<?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
		<br/>
	<?php endif; ?>

	<?php if (in_array('sermon:player', $this->columns)) : ?>
        <?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->item, 'view' => 'sermon')); ?>
	<?php endif; ?>

	<?php if (in_array('sermon:notes', $this->columns) and $this->item->notes) : ?>
		<div>
			<?php echo HTMLHelper::_('content.prepare', $this->item->notes, '', 'com_sermonspeaker.notes'); ?>
		</div>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
