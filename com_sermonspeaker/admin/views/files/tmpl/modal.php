<?php
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$session = Factory::getApplication()->getSession();
?>
<form action="<?php echo OutputFilter::ampReplace(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
	  name="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="btn-group float-end">
				<select name="type" class="form-select" onchange="this.form.submit()">
					<option value="">- <?php echo Text::_('COM_SERMONSPEAKER_FIELD_TYPE_LABEL'); ?> -</option>
					<?php echo HTMLHelper::_('select.options', array('audio' => Text::_('COM_SERMONSPEAKER_AUDIO'), 'video' => Text::_('COM_SERMONSPEAKER_VIDEO')), 'value', 'text', $this->state->get('filter.type'), true); ?>
				</select>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="table table-striped table-hover table-condensed">
			<thead>
			<tr>
				<th class="title"><?php echo Text::_('COM_SERMONSPEAKER_FIELDSET_PATHS_LABEL'); ?></th>
				<th class="text-center"><?php echo Text::_('COM_SERMONSPEAKER_FIELD_TYPE_LABEL'); ?></th>
				<th class="text-center"><?php echo Text::_('JACTION_CREATE'); ?></th>
				<th class="text-center"><?php echo Text::_('JACTION_DELETE'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td><?php echo $item['file']; ?></td>
					<td class="text-center"><?php echo $item['type']; ?></td>
					<td class="text-center">
						<a href="index.php?option=com_sermonspeaker&view=sermon&layout=edit&type=<?php echo $item['type']; ?>&file=<?php echo $item['file']; ?>"
						   target="_parent">
							<span class="fas fa-plus-circle fa-lg text-success hasTooltip"
								  title="<?php echo Text::_('COM_SERMONSPEAKER_NEW_SERMON'); ?>"></span>
						</a>
					</td>
					<td class="text-center">
						<?php if (strpos($item['file'], 'http') !== 0) : ?>
							<a href="index.php?option=com_sermonspeaker&task=tools.delete&file=<?php echo $item['file'] . '&' . $session->getName() . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1"
							   target="_parent">
								<span class="fas fa-trash fa-lg text-danger hasTooltip"
									  title="<?php echo Text::_('COM_SERMONSPEAKER_DELETE_FILE'); ?>"></span>
							</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</form>