<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

JHtml::_('bootstrap.tooltip');

$session = Factory::getApplication()->getSession();
$user    = Factory::getUser();
?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=tools'); ?>" method="post" name="adminForm"
      id="adminForm">
    <div class="row">
        <div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
        </div>
        <div class="col-md-10">
            <div id="j-main-container">
                <div class="card-deck m-3">
                    <a class="card text-center hasTooltip"
                       href="index.php?option=com_sermonspeaker&task=tools.write_id3&<?php echo $session->getName() . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1"
                       title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_ID3_DESC'); ?>"
                    >
                        <div class="card-body">
                            <span class="fa fa-download fa-4x"></span>
                            <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_ID3'); ?></h3>
                        </div>
                    </a>
                    <a class="card text-center hasTooltip"
                       href="#tools-time-modal"
                       data-toggle="modal"
                       title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME_DESC'); ?>"
                    >
                        <div class="card-body">
                            <span class="fa fa-clock-o fa-4x"></span>
                            <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_TIME'); ?></h3>
                        </div>
                    </a>
					<?php echo JHtml::_(
						'bootstrap.renderModal',
						'tools-time-modal',
						array(
							'url'   => Route::_('index.php?option=com_sermonspeaker&view=tools&layout=time&tmpl=component'),
							'title' => Text::_('COM_SERMONSPEAKER_TOOLS_TIME'),
						)
					); ?>
                    <a class="card text-center hasTooltip"
                       href="#tools-files-modal"
                       data-toggle="modal"
                       title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?>"
                    >
                        <div class="card-body">
                            <span class="fa fa-binoculars fa-4x"></span>
                            <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'); ?></h3>
                        </div>
                    </a>
					<?php echo JHtml::_(
						'bootstrap.renderModal',
						'tools-files-modal',
						array(
							'url'        => Route::_('index.php?option=com_sermonspeaker&view=files&layout=modal&tmpl=component'),
							'title'      => Text::_('COM_SERMONSPEAKER_TOOLS_FIND_DESC'),
							'bodyHeight' => 70,
							'modalWidth' => 50,
						)
					); ?>
					<?php // Check Access
					if (!$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker')):
						$link  = 'href="#" onclick="alert(\'' . Text::_('JERROR_ALERTNOAUTHOR') . '\')"';
						$class = ' disabled';
					else:
						$link  = 'href="index.php?option=com_sermonspeaker&task=tools.createAutomatic"';
						$class = '';
					endif; ?>
                    <a class="card text-center hasTooltip<?php echo $class; ?>"
						<?php echo $link; ?>
                       data-placement="left"
                       title="<?php echo Text::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_DESC', Uri::root()); ?>"
                    >
                        <div class="card-body">
                            <span class="fa fa-cogs fa-4x"></span>
                            <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_AUTOMATIC'); ?></h3>
                        </div>
                    </a>
					<?php if ($this->pi) : ?>
                        <a class="card text-center hasTooltip"
                           href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1"
                           title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_DESC'); ?>"
                        >
                            <div class="card-body">
                                <span class="fa-stack fa-2x">
                                    <span class="fa fa-file-o fa-stack-2x"></span>
                                    <span class="fa fa-arrow-left fa-stack-1x"></span>
                                </span>
                                <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT'); ?></h3>
                            </div>
                        </a>
					<?php endif; ?>
					<?php if ($this->bs) : ?>
                        <a class="card text-center hasTooltip"
                           href="index.php?option=com_sermonspeaker&task=tools.piimport&<?php echo $session->getName() . '=' . $session->getId() . '&' . Session::getFormToken(); ?>=1"
                           title="<?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS_DESC'); ?>"
                        >
                            <div class="card-body">
                                <span class="fa-stack fa-2x">
                                    <span class="fa fa-file-o fa-stack-2x"></span>
                                    <span class="fa fa-arrow-left fa-stack-1x"></span>
                                </span>
                                <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_TOOLS_IMPORT_BS'); ?></h3>
                            </div>
                        </a>
					<?php endif; ?>
                    <a class="card text-center" href="index.php?option=com_sermonspeaker&view=statistics&format=raw">
                        <div class="card-body">
                            <span class="fa fa-bar-chart fa-4x"></span>
                            <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_STATISTICS_TITLE'); ?></h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
