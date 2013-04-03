<?php
defined('_JEXEC') or die('Restricted access');

require_once( JApplicationHelper::getPath('toolbar_html'));

switch($task)
{
  case "series":
		TOOLBAR_sermon::_series();
		break;
		
	case 'editSeries':
	  TOOLBAR_sermon::_editSeries();
		break;
		
	case "speakers":
		TOOLBAR_sermon::_speakers();
		break;
		
	case 'editSpeakers':
	  TOOLBAR_sermon::_editSpeakers();
		break;
    	
  case "sermons":
		TOOLBAR_sermon::_sermons();
		break;
		
	case 'editSermons':
	  TOOLBAR_sermon::_editSermons();
		break;	
		
	case 'config':
	  TOOLBAR_sermon::_config();
		break;
	
	case 'add':
		TOOLBAR_sermon::_NEW();
		break;	
		
	case 'edit':
	  TOOLBAR_sermon::_edit();
	  break;
	  
	case 'help':
		TOOLBAR_sermon::_help();
		break;
	
	case 'media':
		TOOLBAR_sermon::_media();
		break;
	
	case 'main':
	  TOOLBAR_sermon::_main();
		break;
		
	case 'stats':
	  TOOLBAR_sermon::_stats();
		break;
	
	case 'comments': 
	case 'saveComment': 
	case 'removeComment': 
		TOOLBAR_reviews_comments::_DEFAULT(); 
		break; 

	case 'editComment': 
		TOOLBAR_reviews_comments::_EDIT(); 
		break;	
		
	default:
		TOOLBAR_sermon::_main();
		break;
}

?>
