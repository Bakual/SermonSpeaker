<?php
defined('_JEXEC') or die('Restricted access');

class TOOLBAR_sermon {
function _series() {
		JToolBarHelper::title( JText::_('Sermon Series'),'generic.png');
		JToolBarHelper::custom('main','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::addNew();
	}
function _edit() {
	  JToolBarHelper::title( JText::_('Edit Speaker'),'generic.png');
		JToolBarHelper::save();
		//JToolBarHelper::apply();
		JToolBarHelper::cancel('series');
	}	
	
function _sermons() {
		JToolBarHelper::title( JText::_('Sermons'),'generic.png');
		JToolBarHelper::custom('main','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList('editSermons');
		JToolBarHelper::deleteList('Do you really want to delete this item?','removeSermons');
		JToolBarHelper::addNew('editSermons');
	}
function _editSermons() {
	  JToolBarHelper::title( JText::_('Edit Sermons'),'generic.png');
		JToolBarHelper::save('saveSermons');
		//JToolBarHelper::apply('saveSermons');
		JToolBarHelper::cancel('sermons');
	}	

function _speakers() {
		JToolBarHelper::title( JText::_('Sermon Speakers'),'generic.png');
		JToolBarHelper::custom('main','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList('editSpeakers');
		JToolBarHelper::deleteList('Do you really want to delete this item?','removeSpeakers');
		JToolBarHelper::addNew('editSpeakers');
	}
function _editSpeakers() {
	  JToolBarHelper::title( JText::_('Edit Speaker'),'generic.png');
		JToolBarHelper::save('saveSpeakers');
		//JToolBarHelper::apply('saveSpeakers');
		JToolBarHelper::cancel('speakers');
	}	

function _config() {
	  JToolBarHelper::title( JText::_('Configuration'),'generic.png');
		JToolBarHelper::save('saveConfig');
		JToolBarHelper::cancel('config');
	}	
	
function _help() {
	  JToolBarHelper::title( JText::_('Help'),'generic.png');
	  JToolBarHelper::custom('main','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
	}	
	
function _media() {
	  JToolBarHelper::title( JText::_('Media Manager'),'generic.png');
	  JToolBarHelper::custom('main','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
	  JToolBarHelper::custom('create_folder','new.png','new.png','Create',false);
	  JToolBarHelper::custom('upload','upload.png','upload.png','Upload',false);
	}	

function _main() {
	  JToolBarHelper::title( JText::_('SermonSpeaker'),'generic.png');
	}	

function _stats() {
	  JToolBarHelper::title( JText::_('Statistics'),'generic.png');
	  JToolBarHelper::custom('showstats','default.png','default.png','Home',false);
	  JToolBarHelper::spacer();
	}	
	
function _NEW() {
	  JToolBarHelper::title( JText::_('Add Entry'),'generic.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
	
function _DEFAULT() {
	  JToolBarHelper::title( JText::_('SermonSpeaker Main'),'generic.png');
	}	
} 

class TOOLBAR_review {
	function _NEW() {
	  JToolBarHelper::title( JText::_( 'Edit / Add' ), 'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}

	function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'Sermon Speaker Default' ), 'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::addNew();
	}
}


class TOOLBAR_reviews_comments 
{
	function _EDIT()
	{
		JToolBarHelper::save('saveComment'); 
		JToolBarHelper::cancel('comments');
	}
	
	function _DEFAULT()
	{
		JToolBarHelper::title( JText::_( 'Comments' ), 'generic.png' );
		JToolBarHelper::editList('editComment');
		JToolBarHelper::deleteList('Are you sure you want to remove these comments?', 'removeComment');
	}
}

?>
