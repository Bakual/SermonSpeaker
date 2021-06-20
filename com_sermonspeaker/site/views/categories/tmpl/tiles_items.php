<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HtmlHelper::_('bootstrap.tooltip');
$class         = 'first';

if (count($this->items[$this->parent->id]) and $this->maxLevelcat != 0) : ?>
	<?php foreach ($this->items[$this->parent->id] as $id => $item) :
		if ($this->params->get('show_empty_categories_cat') or $item->numitems OR $item->hasChildren()) :
			if (!$image = $item->getParams()->get('image')):
				$image = 'media/com_sermonspeaker/images/category.png';
			endif; ?>
			<div class="tile level<?php echo $item->level; ?> <?php echo $class; ?>">
				<?php $class = '';
				$tip         = array();

				if ($this->params->get('show_cat_num_items_cat')) :
					$tip[] = Text::_('COM_SERMONSPEAKER_NUM_ITEMS') . ' ' . $item->numitems;
				endif;

				if ($this->params->get('show_subcat_desc_cat')):
					$tip[] = HtmlHelper::_('content.prepare', $item->description);
				endif;
				$tooltip = implode('<br/>', $tip);
				?>
				<span class="hasTooltip"
					title="<?php echo HtmlHelper::tooltipText($item->title, $tooltip); ?>">
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->id, $item->language)); ?>">
						<img border="0" align="middle" src="<?php echo $image; ?>"/>
						<?php
						if ($item->level == 1) : ?>
							<span class="item-title">
								<?php echo $item->title; ?>
							</span>
						<?php endif; ?>
					</a>
				</span>
				<?php if ($item->hasChildren()) :
					$this->items[$item->id] = $item->getChildren();
					$this->parent           = $item;
					$this->maxLevelcat--;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
				endif; ?>
			</div>
		<?php endif;
	endforeach; ?>
<?php endif;
