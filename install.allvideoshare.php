<?php

/*
 * @version		$Id: install.allvideoshare.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION, '1.6.0', '<')) {
	jimport('joomla.installer.installer');

	$status = new JObject();
	$status->modules = array();
	$status->plugins = array();
	$src = $this->parent->getPath('source');

	// Install Modules
	$mname = 'mod_allvideoshareplayer';
	$installer = new JInstaller;
	$result = $installer->install($src.DS.'modules'.DS.$mname);
	$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);

	$mname = 'mod_allvideosharegallery';
	$installer = new JInstaller;
	$result = $installer->install($src.DS.'modules'.DS.$mname);
	$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);
	
	$mname = 'mod_allvideosharesearch';
	$installer = new JInstaller;
	$result = $installer->install($src.DS.'modules'.DS.$mname);
	$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);

	// Install Plugin
	$pname = 'allvideoshareplayer';
	$installer = new JInstaller;
	$result = $installer->install($src.DS.'plugins'.DS.'allvideoshareplayer');
	$status->plugins[] = array('name'=>$pname, 'group'=>'content', 'result'=>$result);
	
	// Database modifications [start]
	$db = JFactory::getDBO();
	$query = "SELECT COUNT(*) FROM #__allvideoshare_config";
	$db->setQuery($query);
	$num = $db->loadResult();
	
	if($num == 0) {
		$query = "INSERT INTO `#__allvideoshare_players` (`id`, `name`, `width`, `height`, `loop`, `autostart`, `buffer`, `volumelevel`, `stretch`, `durationdock`, `timerdock`, `fullscreendock`, `hddock`, `embeddock`, `facebookdock`, `twitterdock`, `controlbaroutlinecolor`, `controlbarbgcolor`, `controlbaroverlaycolor`, `controlbaroverlayalpha`, `iconcolor`, `progressbarbgcolor`, `progressbarbuffercolor`, `progressbarseekcolor`, `volumebarbgcolor`, `volumebarseekcolor`, `published`) VALUES (1, 'Default', 700, 400, 0, 0, 3, 50, 'uniform', 1, 1, 1, 1, 1, 0, 0, '0x292929', '0x111111', '0x252525', 35, '0xDDDDDD', '0x090909', '0x121212', '0x202020', '0x252525', '0x555555', 1)";
		$db->setQuery($query);
		$db->Query();
	
		$query = "INSERT INTO `#__allvideoshare_config` (`id`, `rows`, `cols`, `thumb_width`, `thumb_height`, `playerid`, `layout`, `relatedvideoslimit`, `title`, `description`, `category`, `views`, `search`, `comments_type`, `comments_posts`, `comments_width`, `comments_color`, `auto_approval`, `type_youtube`, `type_rtmp`, `type_lighttpd`, `type_highwinds`, `type_bitgravity`, `type_thirdparty`, `css`, `cdn_url`, `cdn_username`, `cdn_password`) VALUES (1, 3, 3, 145, 80, 1, 'none', 4, 1, 1, 1, 1, 1, '', 2, 430, 'light', 0, 0, 0, 0, 0, 0, 0, '/* CSS Styles for Gallery Page */\r\n\r\n#avs_gallery .avs_thumb { float:left; margin:7px 14px 7px 0px; padding:0px; }\r\n#avs_gallery .avs_thumb a { text-decoration:none; }\r\n#avs_gallery .avs_thumb .image { display:block; }\r\n#avs_gallery .avs_thumb .arrow { position:absolute; width:29px; height:26px; margin:0px; padding:0px; opacity:1.5; }\r\n#avs_gallery .avs_thumb .name { margin:5px 0px 0px 0px; padding:0px; display:block; font-family:Arial; font-size:11px; color:#777; font-weight:bold; }\r\n#avs_gallery .avs_thumb .title { margin:5px 0px 0px 0px; padding:0px; display:block; font-family:Arial; font-size:12px; color:#444; font-weight:bold; }\r\n#avs_gallery .avs_thumb .views { margin:0px; padding:0px; display:block; font-family:Arial; font-size:11px; color:#777; }\r\n\r\n/* CSS Styles for Pagination */\r\n\r\ndiv #avs_pagination { margin:15px 0px 0px 0px; padding:0px; height:25px; }\r\ndiv #avs_pagination .pagination span, div #avs_pagination .pagination a, div #avs_pagination ul li { margin:0px 2px; padding:3px 7px; background-color:#eee; border:1px solid #ddd; text-align:center; font-size:12px; }\r\ndiv #avs_pagination ul { margin:0px; padding:0px; list-style-type:none; }\r\ndiv #avs_pagination ul li { float:left; line-height:16px; }\r\ndiv #avs_pagination ul li a { text-decoration:none; }\r\n\r\n/* CSS Styles for Video Page */\r\n\r\n#avs_video, .avs_player { margin:0px; padding:0px; }\r\n.avs_video_header { height:25px; margin:10px 0px; padding:0px; color:#777; }\r\n.avs_video_description { margin: 10px 0px; border: 1px solid; padding: 1px 5px; border-radius: 5px; }\r\ndiv.avs_video_tags { margin-top: 5px; }\r\ndiv.avs_video_tags a { text-decoration: none; float: right; padding: 0px 5px; margin: 0px 5px; border-radius: 5px; background-color: #818181; color: #fff !important; border: 1px solid transparent; white-space: nowrap; }\r\ndiv.avs_video_tags a:hover { background-color: #e5e5e5; color: #03ADE5 !important; }\r\n.avs_category_label { margin:10px 0px; padding:0px; float:left; }\r\n.avs_views_label { margin:10px 0px 10px 15px; padding:0px; float:left; }\r\n.avs_input_search { float:right; }\r\n.avs_input_search input { margin:0px; padding:2px 4px; }\r\n.avs_video_comments h2, .avs_video_related h2 { margin:10px 0px; padding:0px; }\r\n\r\n/* CSS Styles for User Page */\r\n\r\ndl.tabs { float: left; margin: 10px 0 -1px 0; z-index: 50; }\r\ndl.tabs dt { float: left; padding: 8px 20px; border: 1px solid #ccc; margin-left: 3px; background: #f0f0f0; color: #666; }\r\ndl.tabs dt.open { background: #FFFFFF; border: 1px solid #ccc; border-bottom: 1px solid #fff; z-index: 100; color: #000; }\r\ndiv.current { clear: both; background-color:#fff; border: 1px solid #fff; border-top: 1px solid #ccc; padding: 10px 10px; }\r\ndiv.current dd { padding: 0; margin: 0; }\r\n\r\n.avs_user table tr, .avs_user table th, .avs_user table td { border:none; margin:0px; padding:7px 10px; }\r\n.avs_user table th { background-color:#E7E7E7; border-bottom:1px solid #CCC; }\r\n.avs_user table tr.row0 { background-color:#F9F9F9; }\r\n.avs_user table tr.row1 { background-color:#F0F0F0; }\r\n.avs_user form { color:#444; overflow:hidden; }\r\n.avs_user form h2 { color:#135CAE; font-weight:bold; margin:15px 0px 5px 0px; }\r\n.avs_user form table tr, .avs_user form  table th, .avs_user form  table td { font-size:11px; color:#444; margin:0px; padding:2px; }\r\n.avskey { font-weight:bold; margin:0px; padding:0px 5px 0px 0px !important; }\r\n.avs_user form textarea { color:#444; }\r\n.avs_user form input, .avs_user form select { margin:0px; padding:3px; float:none; color:#444; }\r\n.avs_user form a:hover { text-decoration:none; }', '', '', '')";
		$db->setQuery($query);
		$db->Query();
	
		$query = "INSERT INTO `#__allvideoshare_licensing` (`id`, `licensekey`, `type`, `logo`, `logoposition`, `logoalpha`, `logotarget`, `displaylogo`) VALUES (1, 'Enter your License Key here...', 'upload', '', 'bottomleft', 50, '', 0)";
		$db->setQuery($query);
		$db->Query();
	}

	$fields_config = $db->getTableFields('#__allvideoshare_config');
	$fields_players = $db->getTableFields('#__allvideoshare_players');
	$fields_categories = $db->getTableFields('#__allvideoshare_categories');
	$fields_videos = $db->getTableFields('#__allvideoshare_videos');
		
	// Update for version 1.1.0
	if (!array_key_exists('thumb_width', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `thumb_width` INT(5) NOT NULL DEFAULT '145', ADD `thumb_height` INT(5) NOT NULL DEFAULT '80' AFTER `cols`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('auto_approval', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `auto_approval` TINYINT(4) NOT NULL DEFAULT '0' AFTER `comments_color`";
		$db->setQuery($query);
		$db->query();
	}

	// Update for version 1.2.0
	if (!array_key_exists('controlbar', $fields_players['#__allvideoshare_players'])) {
		$query = "ALTER TABLE #__allvideoshare_players ADD `controlbar` TINYINT(4) NOT NULL DEFAULT '1', ADD `playlist` TINYINT(4) NOT NULL AFTER `stretch`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('playlistbgcolor', $fields_players['#__allvideoshare_players'])) {
		$query = "ALTER TABLE #__allvideoshare_players ADD `playlistbgcolor` VARCHAR(255) NOT NULL DEFAULT '0x000000', ADD `customplayerpage` VARCHAR(255) NOT NULL AFTER `volumebarseekcolor`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('type_youtube', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `type_youtube` TINYINT(4) NOT NULL DEFAULT '1', ADD `type_rtmp` TINYINT(4) NOT NULL, ADD `type_lighttpd` TINYINT(4) NOT NULL, ADD `type_highwinds` TINYINT(4) NOT NULL, ADD `type_bitgravity` TINYINT(4) NOT NULL, ADD `type_thirdparty` TINYINT(4) NOT NULL DEFAULT '1' AFTER `auto_approval`";
		$db->setQuery($query);
		$db->query();
	}

	// Update for version 1.2.3
	if (!array_key_exists('parent', $fields_categories['#__allvideoshare_categories'])) {
		$query = "ALTER TABLE #__allvideoshare_categories ADD `parent` INT(10) NOT NULL DEFAULT '0' AFTER `slug`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('access', $fields_categories['#__allvideoshare_categories'])) {
		$query = "ALTER TABLE #__allvideoshare_categories ADD `access` VARCHAR(25) NOT NULL, ADD `ordering` INT(5) NOT NULL, ADD `metakeywords` TEXT NOT NULL, ADD `metadescription` TEXT NOT NULL AFTER `thumb`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('metadescription', $fields_videos['#__allvideoshare_videos'])) {
		$query = "ALTER TABLE #__allvideoshare_videos ADD `metadescription` TEXT NOT NULL AFTER `tags`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('access', $fields_videos['#__allvideoshare_videos'])) {
		$query = "ALTER TABLE #__allvideoshare_videos ADD `access` VARCHAR(25) NOT NULL AFTER `views`";
		$db->setQuery($query);
		$db->query();
	}

	if (!array_key_exists('comments_type', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `comments_type` VARCHAR(50) NOT NULL DEFAULT '' AFTER `search`";
		$db->setQuery($query);
		$db->query();
	}
	
	// Update for version 2.0.0
	if (!array_key_exists('fbappid', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `fbappid` VARCHAR(25) NOT NULL AFTER `comments_type`";
		$db->setQuery($query);
		$db->query();
	}
	
	// Update for version 2.1.0	
	$query  = "CREATE TABLE IF NOT EXISTS `#__allvideoshare_adverts` ("."\n";
  	$query .= " `id` int(5) NOT NULL AUTO_INCREMENT,"."\n";
  	$query .= " `title` varchar(255) NOT NULL,"."\n";
  	$query .= " `type` varchar(25) NOT NULL,"."\n";
	$query .= " `method` varchar(25) NOT NULL,"."\n";
  	$query .= " `video` varchar(255) NOT NULL,"."\n";
	$query .= " `link` varchar(255) NOT NULL,"."\n";
	$query .= " `impressions` int(10) NOT NULL,"."\n";
	$query .= " `clicks` int(10) NOT NULL,"."\n";
  	$query .= " `published` tinyint(4) NOT NULL,"."\n";
  	$query .= " PRIMARY KEY (`id`)"."\n";
	$query .= " ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$db->setQuery($query);
	$db->query();
	
	if (!array_key_exists('preroll', $fields_players['#__allvideoshare_players'])) {
		$query = "ALTER TABLE #__allvideoshare_players ADD `preroll` TINYINT(4) NOT NULL, ADD `postroll` TINYINT(4) NOT NULL AFTER `customplayerpage`";
		$db->setQuery($query);
		$db->query();
	}
	
	// Update for version 2.2.0
	if (!array_key_exists('responsive', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `responsive` TINYINT(4) NOT NULL AFTER `id`";
		$db->setQuery($query);
		$db->query();
	}
	
	if(!array_key_exists('cdn_url', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `cdn_url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `css`";
		$db->setQuery($query);
		$db->query();
	}
	if(!array_key_exists('cdn_username', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `cdn_username` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cdn_url`";
		$db->setQuery($query);
		$db->query();
	}
	if(!array_key_exists('cdn_password', $fields_config['#__allvideoshare_config'])) {
		$query = "ALTER TABLE #__allvideoshare_config ADD `cdn_password` VARCHAR(255) NOT NULL DEFAULT '' AFTER `cdn_username`";
		$db->setQuery($query);
		$db->query();
	}
	if(!array_key_exists('uploadable', $fields_categories)) {
		$query = "ALTER TABLE #__allvideoshare_categories ADD `uploadable` tinyint(4) NOT NULL DEFAULT 0 AFTER `metadescription`";
		$db->setQuery($query);
		$db->query();
	}
	// Database modifications [end]
	
	// Remove old version files		
	if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharecategories.php')) {
		JFile::delete(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharecategories.php');
	}
	
	if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideoshareconfig.php')) {
		JFile::delete(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideoshareconfig.php');
	}
		
	if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharelicensing.php')) {
		JFile::delete(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharelicensing.php');
	}
		
	if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideoshareplayers.php')) {
		JFile::delete(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideoshareplayers.php');
	}
		
	if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharevideos.php')) {
		JFile::delete(JPATH_ADMINISTRATOR.'/components/com_allvideoshare/tables/allvideosharevideos.php');
	}

	$rows = 0;
}
?>
<?php if (version_compare(JVERSION, '1.6.0', '<')): ?>
<style type="text/css">
#avs_install table thead tr th, #avs_install table tbody tr th {
	height:25px;
	font-size:12px;
	font-weight:bold;
	padding:5px 0px 5px 10px;
	background:#F0F0F0;
	border:1px solid #E7E7E7;
}
#avs_install table tbody tr td {
	height:25px;
	font-size:11px;
	font-weight:normal;
	padding:5px 0px 5px 10px;
	background:#FFFFFF;
	border:1px solid #E7E7E7;
	color:#333;
}
</style>
<div id="avs_install">
  <table cellspacing="1" cellpadding="0" width="100%">
    <thead>
      <tr>
        <th colspan="2"><?php echo JText::_('Extension'); ?></th>
        <th width="30%"><?php echo JText::_('Status'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="2"><?php echo 'AllVideoShare '.JText::_('Component'); ?></td>
        <td><strong><?php echo JText::_('Installed'); ?></strong></td>
      </tr>
      <?php if (count($status->modules)) : ?>
      <tr>
        <th><?php echo JText::_('Module'); ?></th>
        <th><?php echo JText::_('Client'); ?></th>
        <th></th>
      </tr>
      <?php foreach ($status->modules as $module) : ?>
      <tr>
        <td><?php echo $module['name']; ?></td>
        <td><?php echo ucfirst($module['client']); ?></td>
        <td><strong><?php echo ($module['result']) ? JText::_('Installed') : JText::_('Not installed'); ?></strong></td>
      </tr>
      <?php endforeach;?>
      <?php endif;?>
      <?php if (count($status->plugins)) : ?>
      <tr>
        <th><?php echo JText::_('Plugin'); ?></th>
        <th><?php echo JText::_('Group'); ?></th>
        <th></th>
      </tr>
      <?php foreach ($status->plugins as $plugin) : ?>
      <tr>
        <td><?php echo $plugin['name']; ?></td>
        <td><?php echo ucfirst($plugin['group']); ?></td>
        <td><strong><?php echo ($plugin['result']) ? JText::_('Installed') : JText::_('Not installed'); ?></strong></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif;