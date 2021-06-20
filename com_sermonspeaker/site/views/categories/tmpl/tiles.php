<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

HtmlHelper::_('stylesheet', 'com_sermonspeaker/tiles.css', array('relative' => true));

// Layout supports max 3 level
if ($this->maxLevelcat == -1 or $this->maxLevelcat > 3):
	$this->maxLevelcat = 3;
endif;
?>
<div class="categories-list<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif;

	if ($this->params->get('show_base_description')) :
		if ($this->params->get('categories_description')) : ?>
			<div class="category-desc">
				<p><?php echo HtmlHelper::_('content.prepare', $this->params->get('categories_description')); ?></p>
			</div>
		<?php elseif ($this->parent->description) : ?>
			<div class="category-desc">
				<?php echo HtmlHelper::_('content.prepare', $this->parent->description); ?>
			</div>
		<?php endif;
	endif;
	echo $this->loadTemplate('items'); ?>
</div>

