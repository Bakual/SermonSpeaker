<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$class = 'first';

if (count($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
	<?php foreach ($this->children[$this->category->id] as $id => $child) :
		if ($this->params->get('show_empty_categories_cat') or $child->getNumItems(true) or $child->hasChildren()) :
			if (!$image = $child->getParams()->get('image')) :
				$image = 'media/com_sermonspeaker/images/category.png';
			endif; ?>
			<div class="tile level<?php echo $child->level; ?> <?php echo $class; ?>">
				<?php $class = '';
				$tip         = array();

				if ($this->params->get('show_cat_num_items_cat')) :
					$tip[] = JText::_('COM_SERMONSPEAKER_NUM_ITEMS') . ' ' . $child->numitems;
				endif;

				if ($this->params->get('show_subcat_desc_cat')) :
					$tip[] = JHtml::_('content.prepare', $child->description);
				endif;
				$tooltip = implode('<br/>', $tip);
				?>
				<span class="hasTooltip" title="<?php echo JHtml::tooltipText($child->title, $tooltip); ?>">
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($child->id, $child->language)); ?>">
						<img border="0" align="middle" src="<?php echo $image; ?>"/>
						<?php
						if ($child->level == 1) : ?>
							<span class="item-title">
								<?php echo $child->title; ?>
							</span>
						<?php endif; ?>
					</a>
				</span>
				<?php if ($child->hasChildren()) :
					$this->children[$child->id] = $child->getChildren();
					$this->category             = $child;
					$this->maxLevel--;

					if ($this->maxLevel != 0) :
						echo $this->loadTemplate('children');
					endif;
					$this->category = $child->getParent();
					$this->maxLevel++;
				endif; ?>
			</div>
		<?php endif;
	endforeach; ?>
<?php endif;
