<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

/**
 * @var  array                    $displayData Contains the following items:
 * @var  object                   $category    The category object
 * @var  Joomla\Registry\Registry $params      The params
 * @var  array                    $columns     The columns to show
 */
extract($displayData);
?>
<?php if ($params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo htmlspecialchars($params->get('page_heading'), ENT_QUOTES); ?>
		</h1>
	</div>
<?php endif; ?>

<?php if ($params->get('show_category_title', 1)) : ?>
	<?php $htag = $params->get('show_page_heading') ? 'h2' : 'h1'; ?>
	<<?php echo $htag; ?>>
	<?php echo $category->title; ?>
	</<?php echo $htag; ?>>
<?php endif; ?>

<?php if ($params->get('show_cat_tags', 1) && !empty($category->tags->itemTags)) : ?>
	<?php echo LayoutHelper::render('joomla.content.tags', $category->tags->itemTags); ?>
<?php endif; ?>

<?php if ($params->get('show_description', 1) or $params->get('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($params->get('show_description_image') and $category->getParams()->get('image')) : ?>
			<img src="<?php echo $category->getParams()->get('image'); ?>" alt=""/>
		<?php endif;

		if ($params->get('show_description') and $category->description) :
			echo HTMLHelper::_('content.prepare', $category->description, '', 'com_sermonspeaker.category');
		endif; ?>
		<div class="clearfix"></div>
	</div>
<?php endif; ?>
