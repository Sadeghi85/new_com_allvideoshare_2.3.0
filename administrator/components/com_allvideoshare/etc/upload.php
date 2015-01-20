<?php

/*
 * @version		$Id: upload.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

class AllVideoShareUpload {
	
	function doUpload( $fieldName, $dir ) {		
		$fileName = @$_FILES[$fieldName]['name'];
		$fileTemp = @$_FILES[$fieldName]['tmp_name'];
		$fileSize = @$_FILES[$fieldName]['size'];
		
		if( empty($fileName) ) {
			return;
		}
		
		$format = strtolower( JFile::getExt($fileName) );		
		$allowable = explode( ',', 'jpeg,jpg,png,gif,flv,mp4,3g2,3gp,aac,f4b,f4p,f4v,m4a,m4v,mov,sdp,vp6' );
		if(!in_array($format, $allowable)) {
			return;
		}
		
		$imginfo = null;
		$images = explode( ',', 'jpeg,jpg,png,gif');
		if(in_array($format, $images)) {
			if(($imginfo = getimagesize($fileTemp)) === FALSE) {
				return;
			}
		} else {
			$allowed = false;
			$allowed_mime = explode(',', 'image/*,video/*,audio/*');
			$illegal_mime = explode(',', 'application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,application/x-zip,text/plain,text/css,text/html,text/php,text/x-php,application/php,application/x-php,application/x-httpd-php,application/x-httpd-php-source');	
			if(function_exists('finfo_open')) {	
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $fileTemp);				
				finfo_close($finfo);
			} else if(function_exists('mime_content_type')) {			
				$type = mime_content_type($fileTemp);
			}
			
			if(strlen($type) && !in_array($type, $illegal_mime)) {
				list($m1, $m2)= explode('/', $type);
				foreach($allowed_mime as $k => $v) {
                   	list($v1, $v2) = explode('/', $v);
                   	if (($v1 == '*' && $v2 == '*') || ($v1 == $m1 && ($v2 == $m2 || $v2 == '*'))) {
                       	$allowed = true;
                       	break;
                   	}
               	}
				if($allowed == false) return;
			}			
		}
		
		$xss_check = JFile::read($fileTemp, false, 256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>') || stristr($xss_check, '<?php')) {
				return;
			}
		}
 
 		$fileName = JFile::makesafe($fileName);
		$uploadPath = ALLVIDEOSHARE_UPLOAD_BASE . $dir . DS . strtolower($fileName);
 
		if(!JFile::upload($fileTemp, $uploadPath)) {
       		JError::raiseWarning(21, 'ERROR MOVING FILE');
        	return;
		}
		
		return ALLVIDEOSHARE_UPLOAD_BASEURL . $dir . '/' . strtolower($fileName);
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
	
	function doFtpUpload( $fieldName, $ftpSettings ) {		
		$fileName = @$_FILES[$fieldName]['name'];
		$fileTemp = @$_FILES[$fieldName]['tmp_name'];
		$fileSize = @$_FILES[$fieldName]['size'];
		
		if( empty($fileName) ) {
			return;
		}
		
		$format = strtolower( JFile::getExt($fileName) );		
		$allowable = explode( ',', 'jpeg,jpg,png,gif,flv,mp4,3g2,3gp,aac,f4b,f4p,f4v,m4a,m4v,mov,sdp,vp6' );
		if(!in_array($format, $allowable)) {
			return;
		}
		
		$imginfo = null;
		$images = explode( ',', 'jpeg,jpg,png,gif');
		if(in_array($format, $images)) {
			if(($imginfo = getimagesize($fileTemp)) === FALSE) {
				return;
			}
		} else {
			$allowed = false;
			$allowed_mime = explode(',', 'image/*,video/*,audio/*');
			$illegal_mime = explode(',', 'application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,application/x-zip,text/plain,text/css,text/html,text/php,text/x-php,application/php,application/x-php,application/x-httpd-php,application/x-httpd-php-source');	
			if(function_exists('finfo_open')) {	
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $fileTemp);				
				finfo_close($finfo);
			} else if(function_exists('mime_content_type')) {			
				$type = mime_content_type($fileTemp);
			}
			
			if(strlen($type) && !in_array($type, $illegal_mime)) {
				list($m1, $m2)= explode('/', $type);
				foreach($allowed_mime as $k => $v) {
                   	list($v1, $v2) = explode('/', $v);
                   	if (($v1 == '*' && $v2 == '*') || ($v1 == $m1 && ($v2 == $m2 || $v2 == '*'))) {
                       	$allowed = true;
                       	break;
                   	}
               	}
				if($allowed == false) return;
			}			
		}
		
		$xss_check = JFile::read($fileTemp, false, 256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>') || stristr($xss_check, '<?php')) {
				return;
			}
		}
 
		if ($this->detectUTF8($fileName)) {
			$fileName = $this->base64url_encode($fileName).'.'.$format;
		} else {
			$fileName = JFile::makesafe($fileName);
		}
		
		if ($resFtp = @ftp_connect($ftpSettings['address'])) {
			if (@ftp_login($resFtp, $ftpSettings['username'], $ftpSettings['password'])) {
				if (@ftp_pasv($resFtp, true)) {
					@ftp_raw($resFtp, 'OPTS UTF-8 ON');
					
					if ($ftpSettings['directory']) {
						@ftp_mkdir($resFtp, $ftpSettings['directory']);
						@ftp_chdir($resFtp, $ftpSettings['directory']);
					}
					
					if (@ftp_put($resFtp, $fileName, $fileTemp, FTP_BINARY)) {
						return sprintf('%s/%s/%s', $ftpSettings['url'], $ftpSettings['directory'], $fileName);
					}
				}
			}
		}
		
		JError::raiseWarning(21, 'ERROR MOVING FILE');
		return;
    }
		
}