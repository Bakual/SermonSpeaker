<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermonspeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var array $list
 * @var object                    $module
 * @var \Joomla\Registry\Registry $params
 */

$moduleId = str_replace(' ', '', $module->title) . $module->id;
?>
<?php if ($list) : ?>
	<?php foreach ($list as $type => $items) : ?>
		<h4 class="ps-3 pt-3 "><?php echo Text::_('MOD_SERMONSPEAKER_' . $type); ?></h4>
		<table class="table border-bottom" id="<?php echo $moduleId; ?>">
			<thead>
				<tr>
					<th scope="col" class="w-55"><?php echo Text::_('JGLOBAL_TITLE'); ?></th>
					<?php if ($params->get('show_hits')) : ?>
						<th scope="col" class="w-15"><?php echo Text::_('JGLOBAL_HITS'); ?></th>
					<?php endif; ?>
					<?php if ($params->get('show_author', 1)) : ?>
						<th scope="col" class="w-15"><?php echo Text::_('JAUTHOR'); ?></th>
					<?php endif; ?>
					<th scope="col" class="w-15"><?php echo Text::_('JDATE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($items)) : ?>
					<?php foreach ($items as $i => $item) : ?>
						<tr>
							<th scope="row">
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $moduleId . $i, $item->editor, $item->checked_out_time); ?>
								<?php endif; ?>
								<?php if ($item->link) : ?>
									<a href="<?php echo $item->link; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
										<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								<?php else : ?>
									<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
								<?php endif; ?>
								<br>
								<?php if ($params->get('show_counts')) : ?>
									<?php if (isset($item->sermons)) : ?>
										<a href="index.php?option=com_sermonspeaker&view=sermons&filter[<?php echo rtrim($type, 's'); ?>]=<?php echo $item->id; ?>" class="badge bg-info">
											<?php echo Text::_('MOD_SERMONSPEAKER_SERMONS'); ?>: <?php echo $item->sermons; ?>
										</a>
									<?php endif; ?>
									<?php if (isset($item->series)) : ?>
										<a href="index.php?option=com_sermonspeaker&view=series" class="badge bg-info">
											<?php echo Text::_('MOD_SERMONSPEAKER_SERIES'); ?>: <?php echo $item->series; ?>
										</a>
									<?php endif; ?>
								<?php endif; ?>
							</th>
							<?php if ($params->get('show_hits')) : ?>
								<?php $hits = (int) $item->hits; ?>
								<?php $hits_class = ($hits >= 10000 ? 'danger' : ($hits >= 1000 ? 'warning' : ($hits >= 100 ? 'info' : 'secondary'))); ?>
								<td>
									<span class="badge bg-<?php echo $hits_class; ?>"><?php echo $item->hits; ?></span>
								</td>
							<?php endif; ?>
							<?php if ($params->get('show_author', 1)) : ?>
								<td>
									<?php echo $item->author_name; ?>
								</td>
							<?php endif; ?>
							<td>
								<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3">
							<?php echo Text::_('MOD_POPULAR_NO_MATCHING_RESULTS'); ?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endforeach; ?>
<?php endif; ?>

