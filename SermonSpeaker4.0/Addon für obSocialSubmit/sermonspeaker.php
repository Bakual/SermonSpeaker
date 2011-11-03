<?php
/**
 * @author		Thomas Hunziker - www.sermonspeaker.net
 * @package		obSocialSubmit for Joomla
 * @subpackage	SermonSpeaker addon
 * @license		GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE.DS.'components'.DS.'com_obsocialsubmit'.DS.'helpers'.DS.'class.internaddon.php';
class OBSSInAddonSermonspeaker extends OBSSInAddon{

	function __construct($data=null) {
		$this->data 	= $data;
		$this->revent 	= array('onAfterContentSave');
	}

	public function onAfterContentSave(&$params){
		if (JRequest::getVar('option') != 'com_sermonspeaker'){
			return;
		}
		$item	= &$params['article'];
		if (!isset($item->sermon_title)){
			return;
		}
		$isNew	= &$params['isNew'];
		$configs 	= $this->getConfigs();

		$mode	= $configs->get('mode');
		// Checking if I should do something based on action (new/edit) and category
		$action = $configs->get('action');
		if(($isNew && $action=='edit') || (!$isNew && $action=='new')){
			return;
		}
		if ($configs->get('filter')){
			$categories = $configs->get('category');
			$categories = is_array($categories) ? $categories : array($categories);
			if(!in_array($item->catid, $categories)){
				return;
			}
		}


		// Preparing Data
		$img 	= '';
		if($item->picture){
			$img	= $this->makeLink($item->picture);
		} elseif ($item->speaker_id) {
			$db		= &JFactory::getDBO();
			$query	= "SELECT `name`, `pic` FROM #__sermon_speakers WHERE `id` = '".$item->speaker_id."' LIMIT 1";
			$db->setQuery($query);
			$speaker	= $db->loadRow();
			if ($speaker[1]){
				$img	= $this->makeLink($speaker[1]);
			}
		}

		if ($item->alias){
			$slug 	= $item->id.'-'.$item->alias;
		} else {
			$slug	= $item->id;
		}
		$link = JURI::root().'index.php?option=com_sermonspeaker&view=sermon&id='.$slug;

		if ($item->audiofile and $mode == 'audio')
		{
			$file = $this->makeLink($item->audiofile);
			$type = 'song';
		} 
		elseif ($item->videofile and $mode == 'video') 
		{
			$file = $this->makeLink($item->videofile);
			
			$type = 'movie';
			$fileisVimeoFlg =  substr_count($file, 'vimeo.com');
			if($fileisVimeoFlg >= 1 )
			{
				$fileID = str_replace('http://player.vimeo.com/video/','',$file);
				$fileID = str_replace('?title=0&byline=0&portrait=0','',$fileID);
				$fileID = str_replace('http://vimeo.com/','',$fileID);
				$file = 'http://vimeo.com/moogaloop.swf?clip_id='. $fileID.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';				
			}
			
		}
		elseif ($item->videofile == '' and $mode == 'video') 
		{
			$file = $this->makeLink($item->audiofile);
			$type = 'song';			
		}
		else
		{
			$file = $this->makeLink($item->videofile);
			
			$type = 'movie';
			$fileisVimeoFlg =  substr_count($file, 'vimeo.com');
			if($fileisVimeoFlg >= 1 )
			{
				$fileID = str_replace('http://player.vimeo.com/video/','',$file);
				$fileID = str_replace('?title=0&byline=0&portrait=0','',$fileID);
				$fileID = str_replace('http://vimeo.com/','',$fileID);
				$file = 'http://vimeo.com/moogaloop.swf?clip_id='. $fileID.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';				
			}		
		}
		if ($configs->get('fullurl', 0)){
			$url 	= $file;
		} else {
			$url 	= $link;
		}

		require_once JPATH_SITE.DS.'components'.DS.'com_obsocialsubmit'.DS.'helpers'.DS.'shorturls.php';
		if ($configs->get('shorturl', 0)){
			$shorturl 	= ShortUrls::shortUrl($file);
		} else {
			$shorturl 	= ShortUrls::shortUrl($link);
		}

		$template 	= $configs->get('template', '[title] @ [shorturl]');
		$message 	= $template;

		$title 		= $item->sermon_title;
		$message 	= str_replace("[title]", $title, $message);
		$message	= str_replace("[shorturl]", $shorturl, $message);
		$text = '';
		if ($item->notes){
			$text 	= strip_tags($item->notes);
			$text	= preg_replace('/{bib=(.*)}/U', '$1', $text);
			$text	= preg_replace('/{bible(.*)}(.*){\/bible}/U', '$2', $text);
			if ($mode == 'audio'){
				$text	= "\n".$text;
			}
		}

		// Creating the Post Object
		$post_obj 				= new stdClass();
		$post_obj->url 			= $url;
		$post_obj->shorturl 	= $shorturl;
		$post_obj->title 		= $title;
		$post_obj->message 		= $message;
		$post_obj->description 	= $text;
		$post_obj->img 			= $img;
		$post_obj->template 	= $template;
		if ($mode!= 'article'){
			$post_obj->video_url	= $file;
			$post_obj->type		 	= $type;
		}

		return $post_obj;
	}

	// Used for backward compatibility with older obSocialSubmit?
	function buildMessage($array = array()){
		
		$message 	= '';
		if($this->event == 'onAfterContentSave'){
			$message = $this->onAfterContentSave($array['article'], $array['isNew']);
		}
		return $message;
	}

	// probably used to get SEF URL by checking the URL. Removed it as it created an error.
	private function getUrl($unroute_url){
		
		$url_root 	= JURI::root();
		$tp 		= strpos($url_root,'/components/com_obsocialsubmit/');
		$url_root 	= ( $tp ) ? substr( $url_root, 0, $tp + 1 ) : $url_root;
		$urlcommand = $url_root.'components/com_obsocialsubmit/sefurl.php?url='.urlencode($unroute_url);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlcommand);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		$route_url = curl_exec($ch);
		curl_close($ch);
		$tp2 	= strpos($route_url,'/components/com_obsocialsubmit/');
		
		$link 	= '';
		if( $tp2 !== false ) {
			$st 	= $tp2+31;
			$link 	= $url_root.substr($route_url, $st);
		} else {
			$link 	= $url_root.trim($route_url,'/');
		}
		
		$link = str_replace ( '/component/sermonspeaker/sermon/', '/sermonspeaker/sermon/', $link );
		return $link;
	}

	public function getConfigs(){
		if(!$this->configs){
			$db 	= &JFactory::getDBO();
			$sql 	= "
				SELECT `params`
				FROM `#__obsocialsubmit_addons`
				WHERE `file` = 'sermonspeaker.xml' AND `type`='intern'
				LIMIT 1";
			$db->setQuery($sql);
			$paramtext 	= $db->loadResult();
			$this->configs 		= new JParameter( $paramtext );
		}
		return $this->configs;
	}
	
	private function makeLink($path){
		if (!strpos($path, '://')){
			if (substr($path, 0, 1) == '/') {
				$path = substr($path, 1);
			}
			$path = JURI::root().$path;
		}
		return $path;
	}
}