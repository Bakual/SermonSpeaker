<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_installer&view=manage'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="j-main-container">
		<div class="card m-3">
			<h1 class="card-header">SermonSpeaker Language Packs</h1>
			<div class="card-body">
				This language packs were generated directly from Transifex.<br>
				Since the move to Crowdin, those packs are no longer supported.<br>
				If installed, they will override the languagefiles already included in the SermonSpeaker extensions which are always the latest available at release.<br>
				So please uninstall any existing language packs listed below so you don't have outdated translations.
			</div>
		</div>
		<?php foreach ($this->installed as $language) : ?>
			<div>
				<input
					type="button"
					class="btn btn-danger"
					value="<?php echo Text::_('COM_SERMONSPEAKER_UNINSTALL_LANGUAGEPACK'); ?>"
					onclick="document.getElementById('cid').value = '<?php echo $language->extension_id; ?>'; Joomla.submitbutton();"
					title="<?php echo Text::_('COM_SERMONSPEAKER_LANGUAGEPACK_INSTALLED'); ?>"
				/>
				<?php echo $language->name; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<input type="hidden" id="cid" name="cid[]" />
	<input type="hidden" name="task" value="manage.remove" />
	<?php echo JHtml::_('form.token'); ?>
</form>
