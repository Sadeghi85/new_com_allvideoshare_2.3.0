<?php

/*
 * @version		$Id: user.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import libraries
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_allvideoshare'.DS.'models'.DS.'model.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'etc'.DS.'upload.php' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class AllVideoShareModelUser extends AllVideoShareModel {

    function __construct() {
		parent::__construct();
    }
	
	function getconfig() {
         $db = JFactory::getDBO();
         $query = "SELECT * FROM #__allvideoshare_config";
         $db->setQuery( $query );
         $output = $db->loadObjectList();
         return($output);
	}
	
	function getvideos($user) {
		 $mainframe = JFactory::getApplication();
		 $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', 10, 'int');
		 $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		 
		 // In case limit has been changed, adjust it
		 $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		 $this->setState('limit', $limit);
		 $this->setState('limitstart', $limitstart);
		 
    	 $db = JFactory::getDBO();		 
		 $query = "SELECT * FROM #__allvideoshare_videos WHERE user=" . $db->quote( $user );
		 $query .= " ORDER BY ordering";
    	 $db->setQuery ( $query, $limitstart, $limit );
    	 $output = $db->loadObjectList();
         return($output);
	}
	
	function getpagination( $user ) {
    	 jimport( 'joomla.html.pagination' );
		 $pageNav = new JPagination($this->gettotal( $user ), $this->getState('limitstart'), $this->getState('limit'));
         return($pageNav);
	}
	
	function gettotal( $user ) {
         $db = JFactory::getDBO();
         $query = "SELECT COUNT(*) FROM #__allvideoshare_videos WHERE user=" . $db->quote( $user );
         $db->setQuery( $query );
         $output = $db->loadResult();
         return($output);
	}
	
	function getcategories() {
         $db = JFactory::getDBO();
		 $query = 'SELECT * FROM #__allvideoshare_categories';
		 $db->setQuery( $query );
		 $mitems = $db->loadObjectList();
		
		 $children = array();
		 if( $mitems ) {
			foreach ( $mitems as $v ) {
				$v->title = $v->name;
				$v->parent_id = $v->parent;
				$pt = $v->parent;				
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		 }
		
		 $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );		
		 return $list;
	}

	function getrow() {
     	 $db = JFactory::getDBO();
         $row = JTable::getInstance('Videos', 'AllVideoShareTable');
         $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
         $id = $cid[0];
         $row->load($id);

         return $row;
	}
	
	function getcdnsettings() {
		$db = JFactory::getDBO();
        $row = JTable::getInstance('Config', 'AllVideoShareTable');
		// id: 1
        $row->load(1);

		$settings = array();
		$settings['username'] = $row->cdn_username;
		$settings['password'] = $row->cdn_password;
		$settings['address']  = preg_replace('#^(?:http://)?([^/]+).*#i', '$1', $row->cdn_url);
		$settings['url']      = $row->cdn_url;
		
		return $settings;
	}
	
	function detectUTF8($string)
	{
		return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
		)+%xs', $string);
	}
	
	function base64url_encode($s) {
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
	}

	function base64url_decode($s) {
		return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
	}
	
	function savevideo() {
		 $mainframe = JFactory::getApplication();
	  	 $row = JTable::getInstance('Videos', 'AllVideoShareTable');
	  	 $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
      	 $id = $cid[0];
      	 $row->load($id);
	
      	 if(!$row->bind(JRequest::get('post'))) {
		 	JError::raiseError(500, $row->getError());
	  	 }	  	 
		 
		 jimport( 'joomla.filter.output' );
		 $row->title = AllVideoShareFallback::safeString($row->title);
		 
		 $db = JFactory::getDBO();
		 if ($row->id) {
			$query = "SELECT id FROM #__allvideoshare_videos WHERE title='".$row->title."' AND id != ".$row->id;
		 } else {
			$query = "SELECT id FROM #__allvideoshare_videos WHERE title='".$row->title."'";
		 }
		 $db->setQuery($query);
		 $array = (ALLVIDEOSHARE_JVERSION == '3.0') ? $db->loadColumn() : $db->loadResultArray();
		 if ( ! empty($array)) {
			$row->title .= '-'.str_replace('.', '-', microtime(TRUE));
		 }
		 
	  	 if(!$row->slug) $row->slug = $row->title;		
		 //$row->slug = JFilterOutput::stringURLSafe($row->slug);
		 $row->slug = JFilterOutput::stringURLUnicodeSlug($row->slug);
		 
		 $db = JFactory::getDBO();
		 if ($row->id) {
			$query = "SELECT id FROM #__allvideoshare_videos WHERE slug='".$row->slug."' AND id != ".$row->id;
		 } else {
			$query = "SELECT id FROM #__allvideoshare_videos WHERE slug='".$row->slug."'";
		 }
		 $db->setQuery($query);
		 $array = (ALLVIDEOSHARE_JVERSION == '3.0') ? $db->loadColumn() : $db->loadResultArray();
		 if ( ! empty($array)) {
			$row->slug .= '-'.str_replace('.', '-', microtime(TRUE));
		 }
		 
	  	 $row->description = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML);
		 $row->thirdparty = JRequest::getVar('thirdparty', '', 'post', 'string', JREQUEST_ALLOWRAW);
	  
		 if($row->type == 'cdn_upload') {
			//$dir = JFilterOutput::stringURLSafe( $row->category );
			$dir = JFilterOutput::stringURLUnicodeSlug( $row->category );
			$settings = $this->getcdnsettings();
			
			if ($this->detectUTF8($row->category)) {
				$settings['directory'] = $this->base64url_encode($row->category);
			} else {
				$settings['directory'] = JFilterOutput::stringURLSafe( $row->category );
			}
			
			if(!JFolder::exists(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS)) {
				JFolder::create(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS);
			}
			
			$row->video = AllVideoShareUpload::doFtpUpload('cdn_video', $settings);
			
			$row->thumb = AllVideoShareUpload::doUpload('cdn_thumb', $dir);
			$row->preview = AllVideoShareUpload::doUpload('cdn_preview', $dir);
			
			$row->type   = 'lighttpd';
			$row->access = 'admin';
			
			if ( ! $row->video) {
				JError::raiseError(500, 'Upload Faild');
			}
		 }
			 
	  	 // if($row->type != 'youtube') {
			// ###//$dir = JFilterOutput::stringURLSafe( $row->category );
			// $dir = JFilterOutput::stringURLUnicodeSlug( $row->category );
			
		 	// if(!JFolder::exists(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS)) {
				// JFolder::create(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS);
			// }

			// if($row->type == 'upload') {
				// $row->video = AllVideoShareUpload::doUpload('upload_video', $dir);
				// $row->hd = AllVideoShareUpload::doUpload('upload_hd', $dir);
			// }
			
			// if($row->type != 'upload') {
				// $row->video = AllVideoShareFallback::safeString($row->video);
				// $row->hd = AllVideoShareFallback::safeString($row->hd);
			// }
			
			// if($row->type == 'rtmp') {
				// $row->streamer = AllVideoShareFallback::safeString($row->streamer);
			// }
			
	  		// $row->thumb = AllVideoShareUpload::doUpload('upload_thumb', $dir);
			// $row->preview = AllVideoShareUpload::doUpload('upload_preview', $dir);
	  	 // }
	  	 
		 if($row->type == 'youtube') {
	      	$v = $this->getYouTubeVideoId($row->video);
		 	$row->video = 'http://www.youtube.com/watch?v=' . $v;
			if(!$row->thumb) {
          		$row->thumb = 'http://img.youtube.com/vi/'.$v.'/default.jpg';
			}
			if(!$row->preview) {
		 		$row->preview = 'http://img.youtube.com/vi/'.$v.'/0.jpg';
			}
	     }
	  
	  	 if(!$row->thumb && !JRequest::getCmd('upload_thumb')) {
			$row->thumb = JURI::root().'components/com_allvideoshare/assets/default.jpg';
		 }		
		 
		 $row->reorder( "category='" . $row->category . "'" );
		 
	  	 if(!$row->store()){
			JError::raiseError(500, $row->getError() );
	  	 }

		 $itemId = '';
		 if(JRequest::getInt('Itemid')) {
		 	$itemId = '&Itemid=' . JRequest::getInt('Itemid');
		 }
		 $link = JRoute::_( 'index.php?option=com_allvideoshare&view=user' . $itemId, false );
		 
		 $mainframe->redirect($link, JText::_('SAVED'));	 
	}
	
	function getYouTubeVideoId($url) {
    	$video_id = false;
    	$url = parse_url($url);
    	if(strcasecmp($url['host'], 'youtu.be') === 0) {
        	$video_id = substr($url['path'], 1);
    	} else if(strcasecmp($url['host'], 'www.youtube.com') === 0) {
        	if(isset($url['query'])) {
           		parse_str($url['query'], $url['query']);
            	if(isset($url['query']['v'])) {
               		$video_id = $url['query']['v'];
            	}
        	}
			
        	if($video_id == false) {
            	$url['path'] = explode('/', substr($url['path'], 1));
            	if(in_array($url['path'][0], array('e', 'embed', 'v'))) {
                	$video_id = $url['path'][1];
            	}
        	}
    	}
		
    	return $video_id;
	}
	
	function deletevideo() {
		 $mainframe = JFactory::getApplication();
         $cid = JRequest::getVar( 'cid', array(), '', 'array' );
         $db = JFactory::getDBO();
         $cids = implode( ',', $cid );
         if(count($cid)) {
            $query = "DELETE FROM #__allvideoshare_videos WHERE id IN ( $cids )";
            $db->setQuery( $query );
            if (!$db->query()) {
                echo "<script> alert('".$db->getErrorMsg()."');window.history.go(-1); </script>\n";
            }
         }
		
         $itemId = '';
		 if(JRequest::getInt('Itemid')) {
		 	$itemId = '&Itemid=' . JRequest::getInt('Itemid');
		 }
		 $link = JRoute::_( 'index.php?option=com_allvideoshare&view=user' . $itemId, false );
		 
		 $mainframe->redirect($link ); 
	}
		
}