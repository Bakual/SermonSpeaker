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
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user       = JFactory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<div class="com-sermonspeaker-speakers<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speakers-list">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="com-sermonspeaker-speakers__speakers">
			<?php echo $this->loadTemplate('filters'); ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?>
				</div>
			<?php else : ?>
				<ul class="list-group list-group-flush">
					<?php foreach ($this->items as $i => $item) : ?>
						<?php $sep = 0; ?>
						<li class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?> list-group-item">
							<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit">
									<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'speaker')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
									<?php echo $item->title; ?>
								</a>
							</strong>
							<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
							<?php if (in_array('speakers:hits', $this->col_speaker)) : ?>
								<span class="ss-hits badge bg-info float-end">
									<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif; ?>
							<br/>
							<?php if ($item->sermons) :
								$sep = 1; ?>
								<small class="ss-sermons">
									<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#sermons'); ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_SERMONS'); ?></a>
								</small>
							<?php endif;

							if ($item->series) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-series">
									<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#series'); ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_SERIES'); ?></a>
								</small>
							<?php endif;

							if ($item->website) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-website">
									<a href="<?php echo $item->website; ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?></a>
								</small>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HTMLHelper::_('icon.create', $this->category, $this->params, 'speaker'); ?>
			<?php endif; ?>

			<?php if ($this->params->get('show_pagination') and ($this->pagination->pagesTotal > 1)) : ?>
				<div class="pagination">
					<?php if ($this->params->get('show_pagination_results', 1)) : ?>
						<p class="counter float-end">
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
