<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSermons extends JView
{
	function display( $tpl = null )
	{
		$app = JFactory::getApplication();

		$lists['order']		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order",'filter_order','id','cmd' );
		$lists['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order_Dir",'filter_order_Dir','','word' );
		$filter_state		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_state",'filter_state','','word' );
		$filter_catid		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_catid",'filter_catid','','int' );
		$filter_pcast		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_pcast",'filter_pcast','SELECT ZONE','word' );
		$filter_serie		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_serie",'filter_serie','SELECT ZONE','string' );
		$search				= $app->getUserStateFromRequest("com_sermonspeaker.sermons.search",'search','','string' );
		$search				= JString::strtolower( $search );

		$pagination =& $this->get('Pagination');	// Paginationwerte aus Model lesen
		$items	=& $this->get('Sermons');			// Daten aus Model lesen

		$javascript		= 'onchange="document.adminForm.submit();"';
		// build list of series
		// JHTML::_('select.option',  'Value', 'Text', 'Value Name (Objektattribut)', 'Text Name (Objektattribut)' )
		$serielist[]		= JHTML::_('select.option',  '0', JText::_( 'COM_SERMONSPEAKER_SELECT_SERIES' ), 'id', 'series_title' );		// Default Option setzen
		$serielist			= array_merge( $serielist, $this->get('SerieList') );									// Restlichen Optionen füllen mit Daten aus Model
		// JHTML::_('select.genericlist', $SQL Ergebnis, 'Select name und id', 'Select Attribute', 'Value' , 'Text' , Selectedwert )
		$lists['series']		= JHTML::_('select.genericlist', $serielist, 'filter_serie', 'class="inputbox" size="1" '.$javascript ,'id', 'series_title', $filter_serie);
		
		// pcast filter
		$pcast[]		= JHTML::_('select.option', '0', JText::_('COM_SERMONSPEAKER_SELECT_PCAST'), 'value', 'text' );		// Default Option setzen
		$pcast[]		= JHTML::_('select.option', 'P', JText::_('PUBLISHED'), 'value', 'text' );			// Option setzen
		$pcast[]		= JHTML::_('select.option', 'U', JText::_('UNPUBLISHED'), 'value', 'text' );		// Option setzen
		$lists['pcast']	= JHTML::_('select.genericlist', $pcast, 'filter_pcast', 'class="inputbox" size="1" '.$javascript ,'value', 'text', $filter_pcast);

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