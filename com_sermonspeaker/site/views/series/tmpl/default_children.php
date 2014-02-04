<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$class = ' class="first"';

if (count($this->children[$this->category->id]) > 0) :
	foreach($this->children[$this->category->id] as $id => $child) :
		if ($this->params->get('show_empty_categories') or $child->getNumItems(true) or count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif; ?>
			<div<?php echo $class; ?>>
				<?php $class = ''; ?>
				<h3 class="page-header item-title">
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($child->id));?>">
						<?php echo $this->escape($child->title); ?>
					</a>
					<?php if ($this->params->get('show_cat_num_items', 1)) : ?>
						<span class="badge badge-info tip hasTooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_NUM_ITEMS'); ?>">
							<?php echo $child->getNumItems(true); ?>
						</span>
					<?php endif;

					if (count($child->getChildren()) > 0) : ?>
						<a href="#category-<?php echo $child->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right">
							<i class="icon-plus"></i>
						</a>
					<?php endif;?>
				</h3>
				<?php if ($this->params->get('show_subcat_desc') == 1) :
					if ($child->description) : ?>
						<div class="category-desc">
							<?php echo JHtml::_('content.prepare', $child->description, '', 'com_sermonspeaker.category'); ?>
						</div>
					<?php endif;
				endif;

				if (count($child->getChildren()) > 0) : ?>
					<div class="collapse fade" id="category-<?php echo $child->id;?>">
						<?php $this->children[$child->id] = $child->getChildren();
						$this->category = $child;
						$this->maxLevel--;

						if ($this->maxLevel != 0) :
							echo $this->loadTemplate('children');
						endif;
						$this->category = $child->getParent();
						$this->maxLevel++; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif;
	endforeach;
endif;
