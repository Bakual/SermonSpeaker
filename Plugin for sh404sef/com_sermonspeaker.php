<?php
/**
 * SEF plugin for sh404sef!
 *
 * @author      Thomas Hunziker
 * @copyright   Thomas Hunziker - 2013
 * @package     SermonSpeaker
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.0
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * SermonSpeaker sh404SEF extension plugin.
 * This is a standard Joomla! plugin. Install it using
 * Joomla! installer. It will be loaded in the sh404sefextplugins group.
 * 
 * @author Thomas Hunziker
 */
class  Sh404sefExtpluginCom_sermonspeaker extends Sh404sefClassBaseextplugin
{
	protected $_extName = 'com_sermonspeaker';

	/**
	 * Standard constructor don't change
	 */
	public function __construct( $option, $config) 
	{
		parent::__construct( $option, $config);
		$this->_pluginType = Sh404sefClassBaseextplugin::TYPE_SH404SEF_ROUTER;
	}

	/**
	 * @params array $nonSefVars an array of key=>values representing the non-sef vars of the url
	 *                we are trying to SEFy. You can adjust the plugin used depending on the
	 *                request being made (or other elements). For instance, you could use
	 *                a different plugin based on the currently installed version of the extension               
	 */     
	protected function _findSefPluginPath( $nonSefVars = array())
	{
		$this->_sefPluginPath =  JPATH_ROOT . '/plugins/sh404sefextplugins/sermonspeaker/com_sermonspeaker.php';
	}
}