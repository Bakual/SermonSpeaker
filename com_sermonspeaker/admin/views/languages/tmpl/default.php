<?php
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
?>
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_installer&view=manage'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<?php if ($this->xml) : ?>
		<div class="well">
			<h3>SermonSpeaker Language Packs</h3>
			This language packs were generated directly from Transifex.<br>
			Since the move to Crowdin, those packs are no longer supported.<br>
			If installed, they will override the languagefiles already included in the SermonSpeaker extensions which are always the latest available at release.<br>
			So please uninstall any existing language packs listed below so you don't have outdated translations.
		</div>
		<?php foreach ($this->xml->language as $language) : ?>
			<?php if (isset($this->installed[$this->xml->extension_name . '-' . $language->lang_name])) : ?>
				<div>
					<input
						type="button"
						class="btn btn-danger"
						value="<?php echo JText::_('COM_SERMONSPEAKER_UNINSTALL_LANGUAGEPACK'); ?>"
						onclick="document.getElementById('cid').value = '<?php echo $this->installed[$this->xml->extension_name . '-' . $language->lang_name]->extension_id; ?>'; Joomla.submitbutton();"
						title="<?php echo JText::_('COM_SERMONSPEAKER_LANGUAGEPACK_INSTALLED'); ?>"
					/>
					<?php if (isset($language->iso_lang_name)) : ?>
						<?php echo $language->iso_lang_name; ?>
						<?php if (isset($language->iso_country_name) && $language->iso_country_name != '') : ?>
							(<?php echo $language->iso_country_name; ?>)
						<?php endif; ?>
					<?php else : ?>
						<?php echo $language->lang_name; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
<?php else : ?>
		<div class="well">
			<?php echo JText::_($this->prefix.'_XML_ERROR'); ?>
		</div>
	</div>
	<input type="hidden" id="cid" name="cid[]" />
	<input type="hidden" name="task" value="manage.remove" />
	<?php echo JHtml::_('form.token'); ?>
</form>
