<?php

/*
 * @version		$Id: videos.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import libraries
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_allvideoshare'.DS.'models'.DS.'model.php' );
require_once(JPATH_COMPONENT.DS.'etc'.DS.'upload.php');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class AllVideoShareModelVideos extends AllVideoShareModel {

    function __construct() {
		parent::__construct();
    }
	
	function getdata() {
		 $mainframe = JFactory::getApplication();	
		 $option = JRequest::getCmd('option');
		 $view = JRequest::getCmd('view');
		 
		 $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		 $limitstart = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		 $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		 $filter_access = $mainframe->getUserStateFromRequest($option.$view.'filter_access', 'filter_access', -1, 'none');
		 $filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		 $filter_category = $mainframe->getUserStateFromRequest($option.$view.'filter_category', 'filter_category', '', 'string');
		 $search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		 $search = JString::strtolower($search);
		 
	     $db = JFactory::getDBO();
         $query = "SELECT * FROM #__allvideoshare_videos";
		 $where = array();
		 
		 if ($filter_access != - 1) {
			$where[] = "access='{$filter_access}'";
		 }
		 
		 if ($filter_state > -1) {
			$where[] = "published={$filter_state}";
		 }
		 
		 if ($filter_category && $filter_category != JText::_('SELECT_BY_CATEGORY')) {
			$where[] = 'category='.$db->Quote($filter_category);
		 }
		
		 if ( $search ) {
		 	$escaped = (ALLVIDEOSHARE_JVERSION == '3.0') ? $db->escape( $search, true ) : $db->getEscaped( $search, true );
			//$where[] = 'LOWER(title) LIKE '.$db->Quote( '%'.$escaped.'%', false );
			$escaped = $db->Quote( '%'.$escaped.'%', false );
			$where[] = sprintf('(LOWER(title) LIKE %s OR LOWER(user) LIKE %s OR LOWER(category) LIKE %s)', $escaped, $escaped, $escaped);
		 }

		 $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		 
		 $query .= $where;
		 $query .= " ORDER BY category,ordering";
         $db->setQuery( $query, $limitstart, $limit );
         $output = $db->loadObjectList();
		 
         return($output);
	}
	
	function gettotal() {
		 $mainframe = JFactory::getApplication();	
		 $option = JRequest::getCmd('option');
		 $view = JRequest::getCmd('view');
		 
		 $filter_access = $mainframe->getUserStateFromRequest($option.$view.'filter_access', 'filter_access', -1, 'none');
		 $filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		 $filter_category = $mainframe->getUserStateFromRequest($option.$view.'filter_category', 'filter_category', '', 'string');
		 $search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		 $search = JString::strtolower($search);
		 
         $db = JFactory::getDBO();
         $query = "SELECT COUNT(*) FROM #__allvideoshare_videos";
		 $where = array();
		 
		 if ($filter_access != - 1) {
			$where[] = "access='{$filter_access}'";		
		 }
		
		 if ($filter_state > -1) {
			$where[] = "published={$filter_state}";
		 }

		 if ($filter_category && $filter_category != JText::_('SELECT_BY_CATEGORY')) {
			$where[] = 'category='.$db->Quote($filter_category);
		 }
		 
		 if ( $search ) {
		 	$escaped = (ALLVIDEOSHARE_JVERSION == '3.0') ? $db->escape( $search, true ) : $db->getEscaped( $search, true );
			$where[] = 'LOWER(title) LIKE '.$db->Quote( '%'.$escaped.'%', false );
		 }

		 $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		 $query .= $where;

         $db->setQuery( $query );
         $output = $db->loadResult();
         return($output);
	}
	
	function getpagination() {
		 $mainframe = JFactory::getApplication();	
		 $option = JRequest::getCmd('option');
		 $view = JRequest::getCmd('view');
		 
		 $total = $this->gettotal();
		 $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		 $limitstart = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		 $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
     
    	 jimport( 'joomla.html.pagination' );
		 $pageNav = new JPagination($total, $limitstart, $limit);
         return($pageNav);
	}
	
	function getlists() {
		 $mainframe = JFactory::getApplication();	
		 $option = JRequest::getCmd('option');
		 $view = JRequest::getCmd('view');
		 
		 $filter_access = $mainframe->getUserStateFromRequest($option.$view.'filter_access', 'filter_access', -1, 'none' );
		 $filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int' );
		 $filter_category = $mainframe->getUserStateFromRequest($option.$view.'filter_category', 'filter_category', '', 'string');
		 $search = $mainframe->getUserStateFromRequest($option.$view.'search','search','','string');
		 $search = JString::strtolower ( $search );
     
    	 $lists = array ();
		 $lists['search'] = $search;
            
		 $filter_access_options[] = JHTML::_('select.option', -1, JText::_('SELECT_BY_ACCESS'));
		 $filter_access_options[] = JHTML::_('select.option', 'public', JText::_('PUBLIC'));
		 $filter_access_options[] = JHTML::_('select.option', 'registered', JText::_('REGISTERED'));
		 $filter_access_options[] = JHTML::_('select.option', 'admin', JText::_('ADMIN'));
		 $lists['access'] = JHTML::_('select.genericlist', $filter_access_options, 'filter_access', 'onchange="this.form.submit();"', 'value', 'text', $filter_access);
		 
		 $filter_state_options[] = JHTML::_('select.option', -1, JText::_('SELECT_PUBLISHING_STATE'));
		 $filter_state_options[] = JHTML::_('select.option', 1, JText::_('PUBLISHED'));
		 $filter_state_options[] = JHTML::_('select.option', 0, JText::_('UNPUBLISHED'));
		 $lists['state'] = JHTML::_('select.genericlist', $filter_state_options, 'filter_state', 'onchange="this.form.submit();"', 'value', 'text', $filter_state);
		 
		 $category_options[] = JHTML::_('select.option', '', JText::_('SELECT_BY_CATEGORY'));
		 $categories = $this->getcategories();		 
		 foreach ( $categories as $item ) {
			$item->treename = JString::str_ireplace('&#160;', '-', $item->treename);
			$category_options[] = JHTML::_('select.option', $item->name, $item->treename );
		 }
		 $lists['categories'] = JHTML::_('select.genericlist', $category_options, 'filter_category', 'onchange="this.form.submit();"', 'value', 'text', $filter_category);
		 
         return($lists);
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
	
	function save() {
		 $mainframe = JFactory::getApplication();
	  	 $row = JTable::getInstance('Videos', 'AllVideoShareTable');
	  	 $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
      	 $id = $cid[0];
      	 $row->load($id);
	
      	 if(!$row->bind(JRequest::get('post'))) {
		 	JError::raiseError(500, $row->getError() );
	  	 }
	  
	   	 jimport( 'joomla.filter.output' );
	  	 //$row->title = JRequest::getVar('title', '', 'post', 'string', JREQUEST_ALLOWHTML);
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
		 //$row->slug  = JFilterOutput::stringURLSafe($row->slug);
		 $row->slug  = JFilterOutput::stringURLUnicodeSlug($row->slug);
		 
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
		 $row->thirdparty  = JRequest::getVar('thirdparty', '', 'post', 'string', JREQUEST_ALLOWRAW);
	  
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
			
			$row->type = 'lighttpd';
			
			if ( ! $row->video) {
				JError::raiseError(500, 'Upload Faild');
			}
	  	 }
		 
	  	 if($row->type == 'upload') {		
			//$dir = JFilterOutput::stringURLSafe( $row->category );
			$dir = JFilterOutput::stringURLUnicodeSlug( $row->category );
			
		 	if(!JFolder::exists(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS)) {
				JFolder::create(ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS);
			}
		
			$row->video = AllVideoShareUpload::doUpload('upload_video', $dir);
			$row->hd = AllVideoShareUpload::doUpload('upload_hd', $dir);
	  		$row->thumb = AllVideoShareUpload::doUpload('upload_thumb', $dir);
			$row->preview = AllVideoShareUpload::doUpload('upload_preview', $dir);
	  	 }
	  	 
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
		 
		 if($row->type != 'upload' && $row->type != 'youtube') {
			$row->video = AllVideoShareFallback::safeString($row->video);
			$row->hd = AllVideoShareFallback::safeString($row->hd);
		 }
		 
		 if($row->type == 'rtmp') {
			$row->streamer = AllVideoShareFallback::safeString($row->streamer);
		 }
	  
	  	 if(!$row->thumb && !JRequest::getCmd('upload_thumb')) {
			$row->thumb = JURI::root().'components/com_allvideoshare/assets/default.jpg';
		 }
		
		 $row->reorder( "category='" . $row->category . "'" );
		
	  	 if(!$row->store()){
			JError::raiseError(500, $row->getError() );
	  	 }

	  	 switch (JRequest::getCmd('task')) {
        	case 'apply':
            	$msg  = JText::_('CHANGES_SAVED');
             	$link = 'index.php?option=com_allvideoshare&view=videos&task=edit&'. AllVideoShareFallback::getToken() .'=1&'.'cid[]='.$row->id;				
             	break;
        	case 'save':
        	default:
				$msg  = JText::_('SAVED');
             	$link = 'index.php?option=com_allvideoshare&view=videos';
              	break;
      	 }
		 
		 $mainframe->redirect($link, $msg, 'message'); 	 
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
	
	function cancel() {
		 $mainframe = JFactory::getApplication();
		 
		 $link = 'index.php?option=com_allvideoshare&view=videos';
	     $mainframe->redirect($link);
	}	

	function delete() {
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
		
         $mainframe->redirect( 'index.php?option=com_allvideoshare&view=videos' );
	}
	
	function publish() {
		 $mainframe = JFactory::getApplication();
		 $cid = JRequest::getVar( 'cid', array(), '', 'array' );
         $publish = ( JRequest::getCmd('task') == 'publish' ) ? 1 : 0;
			
         $reviewTable = JTable::getInstance('Videos', 'AllVideoShareTable');
         $reviewTable->publish($cid, $publish);
         $mainframe->redirect( 'index.php?option=com_allvideoshare&view=videos' );
    }
	
	function saveorder() {
		 $mainframe = JFactory::getApplication();

		 // Initialize variables
		 $db = JFactory::getDBO();
		 $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		 $total = count( $cid );
		 $order = JRequest::getVar( 'order', array(0), '', 'array' );
		 JArrayHelper::toInteger($order, array(0));
		 
		 $row = JTable::getInstance('Videos', 'AllVideoShareTable');
		 $groupings = array();
		 // update ordering values
		 for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			$groupings[] = $row->category;
 			if ($row->ordering != $order[$i]) {
				$row->ordering  = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		 }
 
		 $groupings = array_unique($groupings);
		 foreach ($groupings as $group) {
			$row->reorder('category = "'.$group.'"');
		 }
 
		 $mainframe->redirect('index.php?option=com_allvideoshare&view=videos', JText::_('NEW_ORDERING_SAVED'), 'message');
	}
	
	function move($direction) {
		 $mainframe = JFactory::getApplication();
		 $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		 $row = JTable::getInstance('Videos', 'AllVideoShareTable');
		 $row->load($cid[0]);
		 $row->move($direction, 'category = "'.$row->category.'"');
		 $row->reorder('category = "'.$row->category.'"');
	  	 $mainframe->redirect('index.php?option=com_allvideoshare&view=videos', JText::_('NEW_ORDERING_SAVED'), 'message');
	}
	
}