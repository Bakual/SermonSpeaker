<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$published = $this->state->get('filter.published');
?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<?php echo JHtml::_('batch.language'); ?>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<?php echo JHtml::_('batch.tag');?>
			</div>
		</div>
	</div>
	<?php if ($published >= 0) : ?>
		<div class="row-fluid">
			<div class="control-group span6">
				<div class="controls">
					<?php echo JLayoutHelper::render('joomla.html.batch.item', array('extension' => 'com_sermonspeaker')) ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
