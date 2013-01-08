<?php
defined('_JEXEC') or die;

JHTML::_('behavior.modal');

$session	= JFactory::getSession();
$user		= JFactory::getUser();
?>
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<?php if ($this->xml) : ?>
	<table class="table">
		<?php foreach ($this->xml->language as $language) : ?>
			<tr>
				<td>
					<?php $class = (isset($this->languages[str_replace('_', '-', $language->lang_name)])) ? 'btn btn-success' : 'btn'; ?>
					<a href="index.php?option=com_installer&view=install&installtype=url&install_url=<?php echo $language->link; ?>" class="<?php echo $class; ?>">
						<?php echo JText::_('COM_SERMONSPEAKER_INSTALL_LANGUAGEPACK'); ?>
					</a>
					<a href="<?php echo $language->link; ?>" target="_blank">
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
					<div class="progress progress-striped" style="margin-bottom: 0px;">
						<div class="bar" style="width: <?php echo $language->completed; ?>%;"></div>
					</div>
				</td>
				<td>
					<?php echo $language->completed; ?>%
				</td>
				<td>
					<div>
						<?php echo $language->created; ?>
						<?php if($this->xml->contribute && $language->completed != 100) : ?>
							<a href="http://transifex.com/projects/p/<?php echo $this->xml->transifex_slug; ?>/language/<?php echo $language->lang_name; ?>" class="btn" target="_blank">
								<?php echo JText::_('COM_SERMONSPEAKER_CONTRIBUTE_NOW'); ?>
							</a>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<div class="well">
		<h4>Warning!</h4>
		I can't access the info myself. You can try to have a look yourself on <a href="http://www.sermonspeaker.net/download/language-packs.html" target="_blank">SermonSpeaker.net</a>
	</div>
<?php endif; ?>
</div>
