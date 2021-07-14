<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.collapse');

$lang   = Factory::getLanguage();
$user   = Factory::getUser();
$groups = $user->getAuthorisedViewLevels();
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
	<?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
		<?php // Check whether category access level allows access to subcategories. ?>
		<?php if (in_array($child->access, $groups)) : ?>
			<?php if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) : ?>
				<div class="com-sermonspeaker-sermons__children">
					<h3 class="page-header item-title">
						<?php if ($lang->isRtl()) : ?>
							<?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
								<span class="badge bg-info hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_NUM_ITEMS'); ?>">
									<?php echo $child->getNumItems(true); ?>
								</span>
							<?php endif; ?>
							<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($child->id, $child->language)); ?>">
								<?php echo $this->escape($child->title); ?></a>
						<?php else : ?>
							<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($child->id, $child->language)); ?>">
								<?php echo $this->escape($child->title); ?></a>
							<?php if ( $this->params->get('show_cat_num_items', 1)) : ?>
								<span class="badge bg-info hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_NUM_ITEMS'); ?>">
									<?php echo $child->getNumItems(true); ?>
								</span>
							<?php endif; ?>
						<?php endif; ?>

						<?php if (count($child->getChildren()) > 0 && $this->maxLevel > 1) : ?>
							<button type="button"
									id="category-btn-<?php echo $child->id; ?>"
									data-bs-target="#category-<?php echo $child->id; ?>"
									data-bs-toggle="collapse"
									class="btn btn-secondary btn-sm float-end"
									aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"
							>
								<span class="icon-plus" aria-hidden="true"></span>
							</button>
						<?php endif; ?>
					</h3>
					<?php if ($this->params->get('show_subcat_desc') == 1) : ?>
						<?php if ($child->description) : ?>
							<div class="category-desc">
								<?php echo HTMLHelper::_('content.prepare', $child->description, '', 'com_sermonspeaker.category'); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if (count($child->getChildren()) > 0 && $this->maxLevel > 1) : ?>
						<div class="collapse" id="category-<?php echo $child->id; ?>">
							<?php
							$this->children[$child->id] = $child->getChildren();
							$this->category = $child;
							$this->maxLevel--;
							echo $this->loadTemplate('children');
							$this->category = $child->getParent();
							$this->maxLevel++;
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
