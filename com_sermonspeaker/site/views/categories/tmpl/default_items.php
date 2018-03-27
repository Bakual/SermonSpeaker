<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$class	= ' class="first"';
$type	= $this->params->get('count_items_type', 'sermons');
$type_function = 'get' . ucfirst($type) . 'Route';

if (count($this->items[$this->parent->id]) and $this->maxLevelcat != 0) : ?>
	<ul>
	<?php foreach($this->items[$this->parent->id] as $id => $item) :
		if ($this->params->get('show_empty_categories_cat') or $item->numitems or $item->hasChildren()) :
			if (!isset($this->items[$this->parent->id][$id + 1])):
				$class = ' class="last"';
			endif; ?>
			<li<?php echo $class; ?>>
			<?php $class = ''; ?>
				<span class="item-title">
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::$type_function($item->id));?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				</span>
				<?php if ($this->params->get('show_subcat_desc_cat') and $item->description) : ?>
					<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $item->description); ?>
					</div>
				<?php endif;

				if ($this->params->get('show_cat_num_items_cat')) : ?>
					<dl class="article-count">
						<dt><?php echo JText::_('COM_SERMONSPEAKER_NUM_ITEMS'); ?></dt>
						<dd><?php echo $item->numitems; ?></dd>
					</dl>
				<?php endif;

				if ($item->hasChildren()) :
					$this->items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
				endif; ?>
			</li>
		<?php endif;
	endforeach; ?>
	</ul>
<?php endif;
