<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeakers extends JView
{
	function display( $tpl = null )
	{
		global $option;

		$app = JFactory::getApplication();

		$lists['order']		= $app->getUserStateFromRequest("$option.speakers.filter_order",'filter_order','id','cmd' );
		$lists['order_Dir']	= $app->getUserStateFromRequest("$option.speakers.filter_order_Dir",'filter_order_Dir','','word' );
		$filter_state		= $app->getUserStateFromRequest("$option.speakers.filter_state",'filter_state','','word' );
		$filter_catid		= $app->getUserStateFromRequest("$option.speakers.filter_catid",'filter_catid','','int' );
		$search				= $app->getUserStateFromRequest("$option.speakers.search",'search','','string' );
		$search				= JString::strtolower( $search );

		$pagination =& $this->get('Pagination');	// Paginationwerte aus Model lesen
		$items	=& $this->get('speakers');			// Daten aus Model lesen

		// build list of categories (Funktion aus Joomla)
		$javascript		= 'onchange="document.adminForm.submit();"';
		$lists['catid'] = JHTML::_('list.category',  'filter_catid', 'com_sermonspeaker', (int) $filter_catid, $javascript );

		// state filter (Funktion aus Joomla)
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// search filter
		$lists['search']= $search;

        // push data into the template
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}