<?php
defined('_JEXEC') or die;
?>
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_installer&view=install'); ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->xml) : ?>
		<div class="well">
			<h3><?php echo $this->xml->title; ?></h3>
			<?php echo (string)$this->xml->description; ?>
		</div>
		<table class="table adminlist">
			<?php foreach ($this->xml->language as $i => $language) :
				$class = 'btn';
				if ((int)$language->completed && isset($this->installed[$this->xml->extension_name.'-'.$language->lang_name])) :
					// language pack is installed
					$creationDate	= $this->installed[$this->xml->extension_name.'-'.$language->lang_name]->creationDate;
					if (strtotime($language->created) > strtotime($creationDate)) :
						$class .= ' btn-warning';
						$text	= JText::_($this->prefix.'_NEWER_LANGUAGEPACK_THAN_INSTALLED');
					else :
						$class .= ' btn-success';
						$text	= JText::_($this->prefix.'_NEWEST_LANGUAGE_INSTALLED');
					endif;
				elseif ((int)$language->completed && isset($this->languages[str_replace('_', '-', $language->lang_name)])) :
					// site language is installed
					if (strtotime($language->created) > strtotime($this->manifest['creationDate'])) :
						$class .= ' btn-primary';
						$text	= JText::_($this->prefix.'_NEWER_LANGUAGEPACK_THAN_EXTENSION');
					else :
						$class .= ' btn-info';
						$text	= JText::_($this->prefix.'_NEWEST_LANGUAGE_INSTALLED');
					endif;
				else :
					// language pack available
					$text	= JText::_($this->prefix.'_SITELANGUAGE_NOT_INSTALLED');
				endif; ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<input type="button" class="<?php echo $class; ?>" value="<?php echo JText::_($this->prefix.'_INSTALL_LANGUAGEPACK'); ?>" onclick="document.getElementById('install_url').value = '<?php echo $language->link; ?>'; Joomla.submitbutton();" title="<?php echo $text; ?>" />
						<a href="<?php echo $language->link; ?>" title="<?php echo JText::sprintf($this->prefix.'_DOWNLOAD_LANGUAGEPACK', $this->site); ?>">
							<?php if (isset($language->iso_lang_name)) : ?>
							<?php echo $language->iso_lang_name; ?>
							<?php if (isset($language->iso_country_name) && $language->iso_country_name != '') : ?>
								(<?php echo $language->iso_country_name; ?>)
							<?php endif; ?>
							<?php else : ?>
								<?php echo $language->lang_name; ?>
							<?php endif; ?>
						</a>
					</td>
					<td width="40%">
						<div class="progress progress-striped">
							<div class="bar" style="width:<?php echo $language->completed; ?>%;"><?php echo $language->completed; ?>%</div>
						</div>
					</td>
					<td>
						<?php echo JHtml::Date($language->created, JText::_('DATE_FORMAT_LC4')); ?>
						&nbsp;
						<?php if($this->xml->contribute && $language->completed != 100) : ?>
							<button><a href="http://transifex.com/projects/p/<?php echo $this->xml->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>" class="btn" target="_blank">
								<?php echo JText::_($this->prefix.'_CONTRIBUTE_NOW'); ?>
							</a></button>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
<?php else : ?>
		<div class="well">
			<?php echo JText::_($this->prefix.'_XML_ERROR'); ?>
		</div>
<?php endif; ?>
	</div>
	<input type="hidden" id="install_url" name="install_url" />
	<input type="hidden" name="installtype" value="url" />
	<input type="hidden" name="task" value="install.install" />
	<?php echo JHtml::_('form.token'); ?>
</form>
