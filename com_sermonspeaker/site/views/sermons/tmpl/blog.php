<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HtmlHelper::_('bootstrap.framework');
HtmlHelper::_('bootstrap.tooltip');

// Needed for pictures in blog layout
HtmlHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif;

	if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
		<h2>
			<?php echo $this->escape($this->params->get('page_subheading'));

			if ($this->params->get('show_category_title')) : ?>
				<span class="subheading-category"><?php echo $this->category->title; ?></span>
			<?php endif; ?>
		</h2>
	<?php endif;

	if ($this->params->get('show_description', 1) or $this->params->get('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') and $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;

			if ($this->params->get('show_description') and $this->category->description) :
				echo HtmlHelper::_('content.prepare', $this->category->description, '', 'com_sermonspeaker.category');
			endif; ?>
			<div class="clearfix"></div>
		</div>
	<?php endif;

	if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'sermons')); ?>
	<?php endif; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
			name="adminForm" class="form-inline">
			<?php
			if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
				echo $this->loadTemplate('filters');
			endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div
					class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
			<?php else : ?>
				<div class="items-leading">
					<?php foreach ($this->items as $i => $item) : ?>
						<div id="sermon<?php echo $i; ?>"
							class="<?php echo ($item->state) ? '' : 'system-unpublished'; ?>">
							<div class="page-header">
								<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
								<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>

								<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) : ?>
									<small class="ss-speaker createdby">
										<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
										<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
									</small>
								<?php endif; ?>
							</div>
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
									<span class="icon-cog"></span>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li class="play-icon"><?php echo HtmlHelper::_('icon.play', $item, $this->params, array('index' => $i)); ?></li>
									<?php
									if (in_array('sermons:download', $this->columns)) :
										if ($item->audiofile) : ?>
											<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'audio')); ?></li>
										<?php endif;

										if ($item->videofile) : ?>
											<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'video')); ?></li>
										<?php endif; ?>
									<?php endif; ?>
									<li class="email-icon"><?php echo HtmlHelper::_('icon.email', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php endif; ?>
								</ul>
							</div>

							<?php echo $item->event->afterDisplayTitle; ?>

							<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($item)) : ?>
								<div class="img-polaroid pull-right item-image sermon-image"><img
										src="<?php echo $picture; ?>"></div>
							<?php endif; ?>
							<div class="article-info sermon-info muted">
								<dl class="article-info">
									<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
									<?php
									if (in_array('sermons:category', $this->columns) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catid, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:series', $this->columns) and $item->series_title) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<span class="icon-drawer-2"></span>
												<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
												<?php
												if ($item->series_state) : ?>
													<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
														<?php echo $this->escape($item->series_title); ?></a>
												<?php else :
													echo $this->escape($item->series_title);
												endif; ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<dd>
											<div class="create">
												<i class="icon-calendar"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
												<?php echo HtmlHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:hits', $this->columns)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:scripture', $this->columns) and $item->scripture) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-quote"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
												<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
												echo HtmlHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-clock"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('sermons:addfile', $this->columns) and $item->addfile) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</div>

							<?php echo $item->event->beforeDisplayContent; ?>

							<?php if (in_array('sermons:notes', $this->columns) and $item->notes) : ?>
								<div>
									<?php echo HtmlHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
								</div>
							<?php endif; ?>

							<?php echo $item->event->afterDisplayContent; ?>

						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
				</div>
			<?php endif;

			if ($this->params->get('show_pagination') and ($this->pagination->pagesTotal > 1)) : ?>
				<div class="pagination">
					<?php if ($this->params->get('show_pagination_results', 1)) : ?>
						<p class="counter pull-right">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</p>
					<?php endif;
					echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<input type="hidden" name="limitstart" value=""/>
		</form>
	</div>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
