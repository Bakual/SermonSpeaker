<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HtmlHelper::_('bootstrap.tooltip', '.hasTooltip');

$session = Factory::getApplication()->getSession();
$user    = Factory::getUser();
?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=tools'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<div class="row row-cols-1 row-cols-md-3 g-2">
					<div class="col">
						<div class="card text-center hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_ID3_DESC'); ?>">
							<div class="card-header">
								<span class="fas fa-download fa-4x m-auto"></span>
							</div>
							<div class="card-body">
								<a class="stretched-link"
								   href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName()
										   . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1">
									<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></h3>
								</a>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="card text-center hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME_DESC'); ?>">
							<div class="card-header">
								<span class="fas fa-clock fa-4x m-auto"></span>
							</div>
							<div class="card-body">
								<a class="stretched-link" data-bs-toggle="modal" data-bs-target="#tools-time-modal">
									<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h3>
								</a>
							</div>
						</div>
						<?php echo HtmlHelper::_(
							'bootstrap.renderModal',
							'tools-time-modal',
							array(
								'url'   => Route::_('index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component'),
								'title' => Text::_('COM_SERMONSPEAKER_TOOLS_TIME'),
							)
						); ?>
					</div>
					<div class="col">
						<div class="card text-center hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?>">
							<div class="card-header">
								<span class="fas fa-binoculars fa-4x m-auto"></span>
							</div>
							<div class="card-body">
								<a class="stretched-link" data-bs-toggle="modal" data-bs-target="#tools-files-modal">
									<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_FIND'); ?></h3>
								</a>
							</div>
						</div>
						<?php echo HtmlHelper::_(
							'bootstrap.renderModal',
							'tools-files-modal',
							array(
								'url'        => Route::_('index.php?option=com_sermonspeaker&view=files&layout=modal&tmpl=component'),
								'title'      => Text::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'),
								'bodyHeight' => 80,
								'modalWidth' => 90,
							)
						); ?>
						<?php // Check Access
						if (!$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker')):
							Text::script('JERROR_ALERTNOAUTHOR');
							$link  = 'href="#" onclick="alert(Joomla.Text._(\'JERROR_ALERTNOAUTHOR\'))"';
							$class = ' disabled';
						else:
							$link  = 'href="index.php?option=com_sermonspeaker&task=tools.createAutomatic"';
							$class = '';
						endif; ?>
					</div>
					<div class="col">
						<div class="card text-center hasTooltip" title="<?php echo Text::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_DESC', Uri::root()); ?>">
							<div class="card-header">
								<span class="fas fa-cogs fa-4x m-auto"></span>
							</div>
							<div class="card-body">
								<a class="stretched-link<?php echo $class; ?>"<?php echo $link; ?>>
									<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></h3>
								</a>
							</div>
						</div>
					</div>
					<?php if ($this->pi) : ?>
						<div class="col">
							<div class="card text-center hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_DESC'); ?>">
								<div class="card-header">
									<span class="fa-stack fa-2x m-auto">
										<span class="fas fa-file fa-stack-2x"></span>
										<span class="fas fa-arrow-left fa-stack-1x fa-inverse"></span>
									</span>
								</div>
								<div class="card-body">
									<a class="stretched-link"
									   href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName()
											   . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1">
										<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></h3>
									</a>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->bs) : ?>
						<div class="col">
							<div class="card text-center hasTooltip" title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS_DESC'); ?>">
								<div class="card-header">
									<span class="fa-stack fa-2x m-auto">
										<span class="fas fa-file fa-stack-2x"></span>
										<span class="fas fa-arrow-left fa-stack-1x fa-inverse"></span>
									</span>
								</div>
								<div class="card-body">
									<a class="stretched-link"
									   href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName()
											   . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1">
										<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS'); ?></h3>
									</a>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="col">
						<div class="card text-center">
							<div class="card-header">
								<span class="fas fa-chart-bar fa-4x m-auto"></span>
							</div>
							<div class="card-body">
								<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=statistics&format=raw">
									<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?></h3>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
