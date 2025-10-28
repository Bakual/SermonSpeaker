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

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->escape($this->state->get('list.ordering'));

$listDirn   = $this->escape($this->state->get('list.direction'));

?>
<div class="com-sermonspeaker-series<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-series-list">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
			  id="adminForm" class="com-sermonspeaker-series__series">
			<?php echo $this->loadTemplate('filters'); ?>
			<?php echo $this->loadTemplate('order'); ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?>
				</div>
			<?php else : ?>
				<ul class="list-group list-group-flush">
					<?php foreach ($this->items as $i => $item) : ?>
						<?php $sep = 0; ?>
						<li class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?> serie-item list-group-item">
							<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit">
									<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
									<?php echo $item->title; ?>
								</a>
							</strong>
							<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
							<?php if (in_array('series:hits', $this->col_serie)) : ?>
								<span class="ss-hits badge bg-info float-end">
									<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif; ?>
							<br/>
							<?php if (in_array('series:speaker', $this->col_serie) and $item->speakers) : ?>
								<small class="ss-speakers">
									<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
									<?php echo $item->speakers; ?>
								</small>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HTMLHelper::_('icon.create', $this->category, $this->params, 'serie'); ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'series', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
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
