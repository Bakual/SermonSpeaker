<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');

$session = JFactory::getApplication()->getSession();
$user    = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=tools'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container">
				<div class="card-deck">
					<a class="card text-center hasTooltip"
						href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName().'='.$session->getId().'&'.JSession::getFormToken(); ?>=1"
						title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3_DESC'); ?>"
					>
						<div class="card-block">
							<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/download.png"; ?>">
							<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></h3>
						</div>
					</a>
					<a class="card text-center hasTooltip"
						href="index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component"
						title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME_DESC'); ?>"
					>
						<div class="card-block">
							<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/clock.png"; ?>"/>
							<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h3>
						</div>
					</a>
					<a class="card text-center hasTooltip"
						href="index.php?option=com_sermonspeaker&view=files&layout=time&tmpl=component"
						title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?>"
					>
						<div class="card-block">
							<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/find.png"; ?>"/>
							<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?></h3>
						</div>
					</a>
					<?php // Check Access
					if (!$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker')):
						$link = 'href="#" onclick="alert(\''.JText::_('JERROR_ALERTNOAUTHOR').'\')"';
						$class = ' disabled';
					else:
						$link = 'href="index.php?option=com_sermonspeaker&task=tools.createAutomatic"';
						$class = '';
					endif; ?>
					<a class="card text-center hasTooltip<?php echo $class; ?>"
						<?php echo $link; ?>
						data-placement="left"
						title="<?php echo JText::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_DESC', JUri::root()); ?>"
					>
						<div class="card-block">
							<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/run.png"; ?>"/>
							<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></h3>
						</div>
					</a>
					<?php if ($this->pi) : ?>
						<a class="card text-center hasTooltip"
							href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken(); ?>=1"
							title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_DESC'); ?>"
						>
							<div class="card-block">
								<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/import.png"; ?>"/>
								<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></h3>
							</div>
						</a>
					<?php endif; ?>
					<?php if ($this->bs) : ?>
						<a class="card text-center hasTooltip"
							href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken(); ?>=1"
							title="<?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS_DESC'); ?>"
						>
							<div class="card-block">
								<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/import.png"; ?>"/>
								<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS'); ?></h3>
							</div>
						</a>
					<?php endif; ?>
					<a class="card text-center" href="index.php?option=com_sermonspeaker&view=statistics&format=raw">
						<div class="card-block">
							<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/stats.png"; ?>"/>
							<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?></h3>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</form>
