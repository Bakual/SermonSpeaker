<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->item);
$htag       = $this->params->get('show_page_heading') ? 'h2' : 'h1';
?>
<div class="com-sermonspeaker-sermon item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/CreativeWork">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<div class="page-header">
		<<?php echo $htag; ?> itemprop="name">
			<?php echo $this->escape($this->item->title); ?>
		</<?php echo $htag; ?>>
		<?php echo LayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => $showState)); ?>
		<?php if (in_array('sermon:speaker', $this->columns) and $this->item->speaker_title) : ?>
			<small class="ss-speaker createdby" itemprop="author" itemscope itemtype="http://schema.org/Person">
				<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
				<?php echo LayoutHelper::render('titles.speaker', array('item' => $this->item, 'params' => $this->params)); ?>
			</small>
		<?php endif; ?>
	</div>
	<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
		<div class="icons">
			<div class="float-end">
				<?php echo HTMLHelper::_('icon.edit', $this->item, $this->params, array('type' => 'sermon')); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<dl class="article-info sermon-info text-muted">
		<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
		<?php if (in_array('sermons:category', $this->columns) and $this->item->category_title) : ?>
			<dd>
				<div class="category-name">
					<span class="icon-folder-open icon-fw"></span>
					<?php echo Text::_('JCATEGORY'); ?>:
					<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($this->item->catid, $this->item->language)); ?>"><?php echo $this->item->category_title; ?></a>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:series', $this->columns) and $this->item->series_title) : ?>
			<dd>
				<div class="ss-sermondetail-info">
					<span class="icon-drawer-2"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
					<?php if ($this->item->series_state) : ?>
						<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug, $this->item->series_catid, $this->item->series_language)); ?>">
							<?php echo $this->escape($this->item->series_title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($this->item->series_title); ?>
					<?php endif; ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:date', $this->columns) and ($this->item->sermon_date != '0000-00-00 00:00:00')) : ?>
			<dd>
				<div class="create">
					<span class="icon-calendar"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
					<?php echo HTMLHelper::date($this->item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:hits', $this->columns)) : ?>
			<dd>
				<div class="hits">
					<span class="icon-eye-open"></span>
					<?php echo Text::_('JGLOBAL_HITS'); ?>:
					<?php echo $this->item->hits; ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:scripture', $this->columns) and $this->item->scripture) : ?>
			<dd>
				<div class="ss-sermondetail-info">
					<span class="icon-quote"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
					<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($this->item->scripture, '; '); ?>
					<?php echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:length', $this->columns) and $this->item->sermon_time != '00:00:00') : ?>
			<dd>
				<div class="ss-sermondetail-info">
					<span class="icon-clock"></span>
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertTime($this->item->sermon_time); ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:addfile', $this->columns) and $this->item->addfile) : ?>
			<dd>
				<div class="ss-sermondetail-info">
					<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($this->item->addfile, $this->item->addfileDesc); ?>
				</div>
			</dd>
		<?php endif; ?>

		<?php if (in_array('sermons:download', $this->columns)) : ?>
			<?php if ($this->item->audiofile) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<span class="icon-download"></span>
						<?php echo HTMLHelper::_('icon.download', $this->item, $this->params, array('type' => 'audio', 'hideIcon' => true)); ?>
					</div>
				</dd>
			<?php endif; ?>

			<?php if ($this->item->videofile) : ?>
				<dd>
					<div class="ss-sermondetail-info">
						<span class="download-icon"></span>
						<?php echo HTMLHelper::_('icon.download', $this->item, $this->params, array('type' => 'video', 'hideIcon' => true)); ?>
					</div>
				</dd>
			<?php endif; ?>
		<?php endif; ?>
	</dl>
	<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags->itemTags)) : ?>
		<?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (in_array('sermon:player', $this->columns)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->item, 'view' => 'sermon')); ?>
	<?php endif; ?>

	<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($this->item)) : ?>
		<figure class="item-image float-end sermon-image">
			<img src="<?php echo $picture; ?>" alt="">
		</figure>
	<?php endif; ?>

	<?php if (in_array('sermon:notes', $this->columns) and $this->item->notes) : ?>
		<div>
			<?php echo HTMLHelper::_('content.prepare', $this->item->notes, '', 'com_sermonspeaker.notes'); ?>
		</div>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
