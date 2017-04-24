<?php
// Will create an XML containing all files in a directory
// Adjust directory here
$path	= '/server/path/to/html/images/stories'; // intern directory path
$URL	= 'http://www.yourdomain.com/images/folder'; // public URL to the directory
// Don't change anything below here, except if you know what you do.
$declaration = '<?xml version="1.0" encoding="UTF-8"?>
<files>
</files>';
$xml = new SimpleXMLElement($declaration);
if (is_dir($path)) {
	if ($handle = opendir($path)){
		while (($file = readdir($handle)) !== false){
			if (is_file($path.'/'.$file)){
			   $child = $xml->addChild('file');
			   $child->addChild('URL', $URL.'/'.$file);
			   $child->addChild('name', $file);
			}
		}
		closedir($handle);
		header("Content-Type: text/xml");
		echo $xml->asXML(); 
	}
}
