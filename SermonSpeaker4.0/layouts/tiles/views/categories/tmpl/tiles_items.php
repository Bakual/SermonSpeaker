<?php
defined('_JEXEC') or die;
$class	= 'first';
$type	= $this->params->get('count_items_type', 'sermons');
$type_function = 'get'.ucfirst($type).'Route';
if (count($this->items[$this->parent->id]) AND $this->maxLevelcat != 0) : ?>
	<?php foreach($this->items[$this->parent->id] as $id => $item) :
		if ($this->params->get('show_empty_categories_cat') OR $item->numitems OR $item->hasChildren()) :
			if (!$image = $item->getParams()->get('image')):
				$image = 'media/com_sermonspeaker/images/category.png';
			endif;
			$title = ($this->params->get('show_cat_num_items_cat')) ? $this->escape($item->title).' ('.$item->numitems.')' : $this->escape($item->title); ?>
			<div class="tile level<?php echo $item->level; ?> <?php echo $class; ?>">
			<?php $class = ''; ?>
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::$type_function($item->id));?>" title="<?php echo $title; ?>">
					<img border="0" align="middle" src="<?php echo $image; ?>"/>
					<?php if ($item->level == 1): ?>
						<span class="item-title">
							<?php echo $title; ?>
						</span>
					<?php endif; ?>
				</a>
				<?php if ($item->hasChildren()) :
					$this->items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
				endif; ?>
			</div>
		<?php endif;
	endforeach; ?>
<?php endif;