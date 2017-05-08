<?php
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
?>
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_installer&view=install'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
				<?php if (!$this->xml) : ?>
					<div class="card card-info">
						<div class="card-block">
							<h4 class="card-title"><?php echo JText::_('NOTICE'); ?></h4>
							<p class="card-text"><?php echo JText::_('COM_SERMONSPEAKER_XML_ERROR'); ?></p>
							<a href="http://www.sermonspeaker.net/download/language-packs.html" class="btn btn-primary" target="_blank">SermonSpeaker.net</a>
						</div>
					</div>
				<?php else : ?>
					<div class="card">
						<div class="card-block">
							<h3 class="card-title"><?php echo $this->xml->title; ?></h3>
							<p class="card-text"><?php echo (string)$this->xml->description; ?></p>
						</div>
					</div>
					<table class="table">
						<?php foreach ($this->xml->language as $language) : ?>
							<?php $class = 'btn'; ?>
							<?php if (isset($this->installed[$this->xml->extension_name . '-' . $language->lang_name])) : ?>
								<?php // Language pack is installed ?>
								<?php $creationDate = $this->installed[$this->xml->extension_name . '-' . $language->lang_name]->creationDate; ?>
								<?php if (strtotime($language->created) > strtotime($creationDate)) : ?>
									<?php $class .= ' btn-warning'; ?>
									<?php $text = JText::_($this->prefix . '_NEWER_LANGUAGEPACK_THAN_INSTALLED'); ?>
								<?php else : ?>
									<?php $class .= ' btn-success'; ?>
									<?php $text = JText::_($this->prefix . '_NEWEST_LANGUAGE_INSTALLED'); ?>
								<?php endif; ?>
							<?php elseif (isset($this->languages[str_replace('_', '-', $language->lang_name)])) : ?>
								<?php // Site language is installed ?>
								<?php if (strtotime($language->created) > strtotime($this->manifest['creationDate'])) : ?>
									<?php $class .= ' btn-primary'; ?>
									<?php $text = JText::_($this->prefix . '_NEWER_LANGUAGEPACK_THAN_EXTENSION'); ?>
								<?php else : ?>
									<?php $class .= ' btn-info'; ?>
									<?php $text = JText::_($this->prefix . '_NEWEST_LANGUAGE_INSTALLED'); ?>
								<?php endif; ?>
							<?php else : ?>
								<?php // Language pack available ?>
								<?php $text = JText::_($this->prefix . '_SITELANGUAGE_NOT_INSTALLED'); ?>
							<?php endif; ?>
							<tr>
								<td>
									<input type="button" class="hasTooltip <?php echo $class; ?>" value="<?php echo JText::_($this->prefix.'_INSTALL_LANGUAGEPACK'); ?>" onclick="document.getElementById('install_url').value = '<?php echo $language->link; ?>'; Joomla.submitbutton();" title="<?php echo $text; ?>" />
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
									<div class="progress">
										<div class="progress-bar progress-bar-striped" role="progressbar"
											 style="width: <?php echo $language->completed; ?>%"
											 aria-valuenow="<?php echo $language->completed; ?>"
											 aria-valuemin="0" aria-valuemax="100">
												<?php echo $language->completed; ?>%
										</div>
									</div>
								</td>
								<td>
									<div>
										<?php echo JHtml::date($language->created, JText::_('DATE_FORMAT_LC4')); ?>
										<?php if ((string) $this->xml->contribute && $language->completed != 100) : ?>
											<a href="http://transifex.com/projects/p/<?php echo $this->xml->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>" class="btn btn-secondary" target="_blank">
												<?php echo JText::_($this->prefix.'_CONTRIBUTE_NOW'); ?>
											</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<input type="hidden" id="install_url" name="install_url" />
	<input type="hidden" name="installtype" value="url" />
	<input type="hidden" name="task" value="install.install" />
	<?php echo JHtml::_('form.token'); ?>
</form>
