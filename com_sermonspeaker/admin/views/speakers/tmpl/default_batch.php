<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

$published = $this->state->get('filter.state');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body modal-batch">
		<p><?php echo JText::_('COM_SERMONSPEAKER_BATCH_TIP'); ?></p>
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
						<?php echo JHtml::_('batch.item', 'com_sermonspeaker');?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal"
			onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-language-id').value='';document.getElementById('batch-tag-id').value=''">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('speaker.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
