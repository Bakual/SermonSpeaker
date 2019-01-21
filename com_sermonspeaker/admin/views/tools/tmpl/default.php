<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

JHtml::_('behavior.modal');

$session = JFactory::getSession();
$user    = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=tools'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if(!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
		<ul class="thumbnails">
			<li class="span2" rel="tooltip" data-placement="right" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3_DESC'); ?>">
				<a class="thumbnail" href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/download.png"; ?>"/>
					<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></h3>
				</a>
			</li>
			<li class="span2" rel="tooltip" data-placement="right" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME_DESC'); ?>">
				<a class="modal thumbnail" href="index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component" rel="{handler: 'iframe', size: {x: 450, y: 170}}" >
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/clock.png"; ?>"/>
					<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h3>
				</a>
			</li>
			<li class="span2" rel="tooltip" data-placement="right" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?>">
				<a class="modal thumbnail" href="index.php?option=com_sermonspeaker&view=files&layout=modal&tmpl=component" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/find.png"; ?>"/>
					<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?></h3>
				</a>
			</li>
			<?php // Check Access
			if (!$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker')):
				$link = 'href="#" onclick="alert(\''.JText::_('JERROR_ALERTNOAUTHOR').'\')"';
				$class = ' disabled';
			else:
				$link = 'href="index.php?option=com_sermonspeaker&task=tools.createAutomatic"';
				$class = '';
			endif; ?>
			<li class="span2<?php echo $class; ?>" rel="tooltip" data-placement="left" title="<?php echo JText::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_DESC', JUri::root()); ?>">
				<a class="thumbnail" <?php echo $link; ?>>
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/run.png"; ?>"/>
					<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></h3>
				</a>
			</li>
			<?php if($this->pi) : ?>
				<li class="span2" rel="tooltip" data-placement="left" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_DESC'); ?>">
					<a class="thumbnail" href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken(); ?>=1">
						<img src="<?php echo JUri::base() . "components/com_sermonspeaker/images/import.png"; ?>"/>
						<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></h3>
					</a>
				</li>
			<?php endif; ?>
			<?php if($this->bs) : ?>
				<li class="span2" rel="tooltip" data-placement="left" title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS_DESC'); ?>">
					<a class="thumbnail" href="index.php?option=com_sermonspeaker&task=tools.bsimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken(); ?>=1">
						<img src="<?php echo JUri::base() . "components/com_sermonspeaker/images/import.png"; ?>"/>
						<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS'); ?></h3>
					</a>
				</li>
			<?php endif; ?>
			<li class="span2">
				<a class="thumbnail" href="index.php?option=com_sermonspeaker&view=statistics&format=raw">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/stats.png"; ?>"/>
					<h3 class="center"><?php echo JText::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?></h3>
				</a>
			</li>
		</ul>
	</div>
</form>
