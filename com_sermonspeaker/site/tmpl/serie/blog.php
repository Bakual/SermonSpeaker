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

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// Needed for pictures in blog layout
HTMLHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->escape($this->state->get('list.ordering'));

$listDirn   = $this->escape($this->state->get('list.direction'));

$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelper::getPlayer($this->items);
?>
<div class="com-sermonspeaker-serie<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-serie-blog">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'serie')); ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
			  id="adminForm" class="com-sermonspeaker-serie__sermons">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
				</div>
			<?php else : ?>
				<div class="items-leading">
					<?php foreach ($this->items as $i => $item) : ?>
						<div id="sermon<?php echo $i; ?>"
							 class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>sermon-item">
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
									<i class="icon-cog"></i>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<?php if ($playerid = !empty($player->id) ? $player->id : '') : ?>
										<li class="play-icon"><?php echo HTMLHelper::_('icon.play', $item, $this->params, array('index' => $i, 'playerid' => $playerid)); ?></li>
									<?php endif; ?>
									<?php
									if (in_array('serie:download', $this->columns)) :
										if ($item->audiofile) : ?>
											<li class="download-icon"><?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'audio')); ?></li>
										<?php endif;

										if ($item->videofile) : ?>
											<li class="download-icon"><?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'video')); ?></li>
										<?php endif;
									endif; ?>
									<?php
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="page-header">
								<h2><?php echo SermonspeakerHelper::insertSermonTitle($i, $item, $player, false); ?></h2>
								<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>

								<?php if (in_array('serie:speaker', $this->columns) and $item->speaker_title) : ?>
									<small class="ss-speaker createdby">
										<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
										<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
									</small>
								<?php endif; ?>
							</div>

							<?php echo $item->event->afterDisplayTitle; ?>

							<?php if ($picture = SermonspeakerHelper::insertPicture($item)) : ?>
								<div class="img-thumbnail pull-right item-image sermon-image"><img
											src="<?php echo $picture; ?>"></div>
							<?php endif; ?>
							<div class="article-info sermon-info muted">
								<dl class="article-info">
									<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
									<?php
									if (in_array('serie:category', $this->columns) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonsRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<dd>
											<div class="create">
												<i class="icon-calendar"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
												<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:hits', $this->columns)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:scripture', $this->columns) and $item->scripture) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-quote"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
												<?php $scriptures = SermonspeakerHelper::insertScriptures($item->scripture, '; ');
												echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-clock"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
												<?php echo SermonspeakerHelper::insertTime($item->sermon_time); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:addfile', $this->columns) and $item->addfile) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
												<?php echo SermonspeakerHelper::insertAddfile($item->addfile, $item->addfileDesc); ?>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</div>

							<?php echo $item->event->beforeDisplayContent; ?>

							<?php if (in_array('serie:notes', $this->columns) and $item->notes) : ?>
								<div class="ss-notes">
									<?php echo HTMLHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
								</div>
							<?php endif; ?>

							<?php if (in_array('serie:maintext', $this->columns) and $item->maintext) : ?>
								<div class="ss-maintext">
									<?php echo HTMLHelper::_('content.prepare', $item->maintext, '', 'com_sermonspeaker.maintext'); ?>
								</div>
							<?php endif; ?>

							<?php echo $item->event->afterDisplayContent; ?>

						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'serie', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<input type="hidden" name="limitstart" value=""/>
		</form>
	</div>
</div>
