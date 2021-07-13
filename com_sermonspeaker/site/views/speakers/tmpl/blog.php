<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
HTMLHelper::_('bootstrap.dropdown');

$user       = JFactory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-speakers-container<?php echo $this->pageclass_sfx; ?>">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm"
			name="adminForm">
			<?php
			if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<div class="filters btn-toolbar">
					<?php if ($this->params->get('show_pagination_limit')) : ?>
						<div class="btn-group pull-right">
							<label class="element-invisible">
								<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
							</label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div
					class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
			<?php else : ?>
				<div class="items-leading">
					<?php foreach ($this->items as $i => $item) : ?>
						<div class="<?php echo ($item->state) ? '' : 'system-unpublished'; ?>">
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
									<i class="icon-cog"></i>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<?php
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'speaker')); ?></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="page-header">
								<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
									<h2><?php echo $item->title; ?></h2>
								</a>
								<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
							</div>

							<?php echo $item->event->afterDisplayTitle; ?>

							<?php if ($item->pic) : ?>
								<div class="img-polaroid pull-right item-image">
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
										<img
											src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->pic); ?>">
									</a>
								</div>
							<?php endif; ?>
							<div class="article-info speaker-info muted">
								<dl class="article-info">
									<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
									<?php
									if (in_array('speakers:category', $this->col_speaker) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakersRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;

									if (in_array('speakers:hits', $this->col_speaker)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif;

									if ($item->website) : ?>
										<dd>
											<div class="website">
												<i class=" icon-out-2"></i>
												<a href="<?php echo $item->website; ?>">
													<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
												</a>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</div>

							<?php echo $item->event->beforeDisplayContent; ?>

							<?php if (in_array('speakers:intro', $this->col_speaker) and $item->intro) : ?>
								<div>
									<?php echo HTMLHelper::_('content.prepare', $item->intro, '', 'com_sermonspeaker.intro'); ?>
								</div>
							<?php endif; ?>

							<?php if (in_array('speakers:bio', $this->col_speaker) and $item->bio) : ?>
								<div>
									<?php echo HTMLHelper::_('content.prepare', $item->bio, '', 'com_sermonspeaker.bio'); ?>
								</div>
							<?php endif; ?>

							<?php if ($item->sermons) : ?>
								<a class="badge badge-info"
									title="<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>"
									href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#sermons'); ?>">
									<?php echo Text::_('COM_SERMONSPEAKER_SERMONS') . ': ' . $item->sermons; ?></a>&nbsp;
							<?php endif; ?>

							<?php if ($item->series) : ?>
								<a class="badge badge-info"
									title="<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>"
									href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#series'); ?>">
									<?php echo Text::_('COM_SERMONSPEAKER_SERIES') . ': ' . $item->series; ?></a>&nbsp;
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
