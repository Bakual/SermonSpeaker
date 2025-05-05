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
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="com-sermonspeaker-sermons<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-sermons-blog blog">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<?php if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'sermons')); ?>
	<?php endif; ?>
	<div class="com-sermonspeaker-sermons-blog__items blog-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
			  name="adminForm" class="com-sermonspeaker-sermons__sermons">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
				<?php echo $this->loadTemplate('order'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
				</div>
			<?php else : ?>
				<?php foreach ($this->items as $i => $item) : ?>
					<div id="sermon<?php echo $i; ?>"
						 class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>sermon-item image-right">
						<div class="com-sermonspeaker-sermons-blog__item blog-item">
							<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($item)) : ?>
								<figure class="item-image sermon-image">
									<img src="<?php echo $picture; ?>" alt="">
								</figure>
							<?php endif; ?>

							<div class="item-content">
								<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
								<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) : ?>
									<small class="com-sermonspeaker-speaker createdby">
										<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
										<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
									</small>
								<?php endif; ?>

								<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
									<div class="icons">
										<div class="float-end">
											<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php echo $item->event->afterDisplayTitle; ?>

								<dl class="article-info sermon-info text-muted">
									<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
									<?php if (in_array('sermons:category', $this->columns) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<span class="icon-folder-open icon-fw"></span>
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catid, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:series', $this->columns) and $item->series_title) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<span class="icon-drawer-2"></span>
												<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
												<?php if ($item->series_state) : ?>
													<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
														<?php echo $this->escape($item->series_title); ?></a>
												<?php else : ?>
													<?php echo $this->escape($item->series_title); ?>
												<?php endif; ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<dd>
											<div class="create">
												<span class="icon-calendar"></span>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
												<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:hits', $this->columns)) : ?>
										<dd>
											<div class="hits">
												<span class="icon-eye-open"></span>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:scripture', $this->columns) and $item->scripture) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<span class="icon-quote"></span>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
												<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; '); ?>
												<?php echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<span class="icon-clock"></span>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:addfile', $this->columns) and $item->addfile) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if ($playerid = !empty($player->id) ? $player->id : '') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo HTMLHelper::_('icon.play', $item, $this->params, array('index' => $i, 'playerid' => $playerid)); ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('sermons:download', $this->columns)) : ?>
										<?php if ($item->audiofile) : ?>
											<dd>
												<div class="ss-sermondetail-info">
													<span class="icon-download"></span>
													<?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'audio', 'hideIcon' => true)); ?>
												</div>
											</dd>
										<?php endif; ?>

										<?php if ($item->videofile) : ?>
											<dd>
												<div class="ss-sermondetail-info">
													<span class="download-icon"></span>
													<?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'video', 'hideIcon' => true)); ?>
												</div>
											</dd>
										<?php endif; ?>
									<?php endif; ?>
								</dl>

								<?php echo $item->event->beforeDisplayContent; ?>

								<?php if (in_array('sermons:notes', $this->columns) and $item->notes) : ?>
									<div>
										<?php echo HTMLHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
									</div>
								<?php endif; ?>

								<?php if (in_array('sermons:maintext', $this->columns) and $item->maintext) : ?>
									<div>
										<?php echo HTMLHelper::_('content.prepare', $item->maintext, '', 'com_sermonspeaker.maintext'); ?>
									</div>
								<?php endif; ?>

								<?php echo $item->event->afterDisplayContent; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'sermons', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
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
