<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$class = ' class="first"';

if (count($this->children[$this->category->id]) > 0) : ?>
	<ul>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
		if ($this->params->get('show_empty_categories') or $child->getNumItems(true) or count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>

		<li<?php echo $class; ?>>
			<?php $class = ''; ?>
			<span class="item-title"><a href="<?php echo JRoute::_('index.php?view=seriessermon&catid=' . $child->id); ?>">
				<?php echo $this->escape($child->title); ?></a>
			</span>
			<?php if ($this->params->get('show_subcat_desc') == 1) :
				if ($child->description) : ?>
					<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $child->description); ?>
					</div>
				<?php endif;
			endif;

			if ( $this->params->get('show_cat_num_items', 1)) : ?>
			<dl class="article-count">
				<dt><?php echo JText::_('COM_SERMONSPEAKER_NUM_ITEMS'); ?></dt>
				<dd><?php echo $child->getNumItems(true); ?></dd>
			</dl>
			<?php endif;

			if (count($child->getChildren()) > 0 ) :
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;

				if ($this->maxLevel != 0) :
					echo $this->loadTemplate('children');
				endif;
				$this->category = $child->getParent();
				$this->maxLevel++;
			endif; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif;
