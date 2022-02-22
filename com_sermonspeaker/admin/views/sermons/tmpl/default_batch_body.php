<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$published = $this->state->get('filter.published');
?>

<div class="container">
	<div class="row">
		<div class="form-group col-md-6">
			<div class="controls">
				<label id="batch-speaker-lbl" for="batch-speaker-id">
					<?php echo Text::_('COM_SERMONSPEAKER_BATCH_SPEAKER_LABEL'); ?>
				</label>
				<select name="batch[speaker_id]" class="custom-select" id="batch-speaker-id">
					<option value=""><?php echo Text::_('COM_SERMONSPEAKER_BATCH_SPEAKER_NOCHANGE'); ?></option>
					<?php echo HTMLHelper::_('select.options', $this->speakers, 'value', 'text'); ?>
				</select>
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="controls">
				<label id="batch-serie-lbl" for="batch-serie-id">
					<?php echo Text::_('COM_SERMONSPEAKER_BATCH_SERIE_LABEL'); ?>
				</label>
				<select name="batch[serie_id]" class="custom-select" id="batch-serie-id">
					<option value=""><?php echo Text::_('COM_SERMONSPEAKER_BATCH_SERIE_NOCHANGE'); ?></option>
					<?php echo HTMLHelper::_('select.options', $this->series, 'value', 'text'); ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.tag', array()); ?>
			</div>
		</div>
	</div>
	<?php if ($published >= 0) : ?>
		<div class="row">
			<div class="form-group col-md-6">
				<div class="controls">
					<?php echo LayoutHelper::render('joomla.html.batch.item', array('extension' => 'com_sermonspeaker.sermons')) ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
