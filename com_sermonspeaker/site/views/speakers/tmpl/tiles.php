<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');
?>
<div class="com-sermonspeaker-speakers<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speakers-tiles">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
		  id="adminForm" class="com-sermonspeaker-speakers__speakers">
		<?php echo $this->loadTemplate('filters'); ?>
		<?php echo $this->loadTemplate('order'); ?>
		<div class="clearfix"></div>
		<?php if (!count($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span
						class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?>
			</div>
		<?php else : ?>
			<div class="row row-cols-1 row-cols-md-4 g-4">
				<?php foreach ($this->items as $i => $item) : ?>
					<?php $speakerUrl = Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>
					<?php $image = ($item->pic) ?: 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
					<div class="col">
						<div id="speaker<?php echo $i; ?>" class="ss-entry card h-100">
							<a href="<?php echo $speakerUrl; ?>">
								<img class="card-img-top" src="<?php echo trim($image, '/'); ?>">
							</a>
							<div class="card-body">
								<a href="<?php echo $speakerUrl; ?>">
									<h5 class="card-title"><?php echo $item->title; ?></h5>
								</a>
								<div class="card-text">
									<?php if (in_array('speakers:category', $this->col_speaker)) : ?>
										<?php echo Text::_('JCATEGORY') . ': ' . $item->category_title; ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speakers:hits', $this->col_speaker) and $item->hits) : ?>
										<?php echo Text::_('JGLOBAL_HITS') . ': ' . $item->hits; ?>
										<br>
									<?php endif; ?>
								</div>
							</div>
							<?php if ((in_array('speakers:intro', $this->col_speaker) and $item->intro) or (in_array('speakers:bio', $this->col_speaker) and $item->bio)) : ?>
								<div class="card-footer text-muted">
									<?php if (in_array('speakers:intro', $this->col_speaker) and $item->intro) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_INTRO_LABEL') . ': ' . HTMLHelper::_('content.prepare', $item->intro); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speakers:bio', $this->col_speaker) and $item->bio) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_BIO_LABEL') . ': ' . HTMLHelper::_('content.prepare', $item->bio); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'speakers', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
