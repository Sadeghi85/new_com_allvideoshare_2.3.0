<?php

/*
 * @version		$Id: uninstall.allvideoshare.php 2.3.0 2014-06-21 $
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
	$db = JFactory::getDBO();
	
	// UnInstall Modules	
	$mname = 'mod_allvideoshareplayer';
	$query = "SELECT `id` FROM `#__modules` WHERE module = ".$db->Quote($mname);
	$db->setQuery($query);
	$modules = $db->loadResultArray();
	if(count($modules)) {
		foreach ($modules as $module) {
			$installer = new JInstaller;
			$result = $installer->uninstall('module', $module);
		}
		$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);
	}
	
	$mname = 'mod_allvideosharegallery';
	$query = "SELECT `id` FROM `#__modules` WHERE module = ".$db->Quote($mname);
	$db->setQuery($query);
	$modules = $db->loadResultArray();
	if(count($modules)) {
		foreach ($modules as $module) {
			$installer = new JInstaller;
			$result = $installer->uninstall('module', $module);
		}
		$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);
	}
	
	$mname = 'mod_allvideosharesearch';
	$query = "SELECT `id` FROM `#__modules` WHERE module = ".$db->Quote($mname);
	$db->setQuery($query);
	$modules = $db->loadResultArray();
	if(count($modules)) {
		foreach ($modules as $module) {
			$installer = new JInstaller;
			$result = $installer->uninstall('module', $module);
		}
		$status->modules[] = array('name'=>$mname, 'client'=>'site', 'result'=>$result);
	}

	// UnInstall Plugin
	$pname = 'allvideoshareplayer';
	$query = 'SELECT `id` FROM #__plugins WHERE element = '.$db->Quote($pname);
	$db->setQuery($query);
	$plugins = $db->loadResultArray();
	if(count($plugins)) {
		foreach ($plugins as $plugin) {
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $plugin, 0);
		}
		$status->plugins[] = array('name'=>$pname, 'group'=>'content', 'result'=>$result);
	}

	$rows = 0;
}
?>
<?php if (version_compare(JVERSION, '1.6.0', '<')): ?>
<style type="text/css">
#avs_uninstall table thead tr th, #avs_uninstall table tbody tr th {
	height:25px;
	font-size:12px;
	font-weight:bold;
	padding:5px 0px 5px 10px;
	background:#F0F0F0;
	border:1px solid #E7E7E7;
}
#avs_uninstall table tbody tr td {
	height:25px;
	font-size:11px;
	font-weight:normal;
	padding:5px 0px 5px 10px;
	background:#FFFFFF;
	border:1px solid #E7E7E7;
	color:#333;
}
</style>
<div id="avs_uninstall">
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
        <td><strong><?php echo JText::_('Removed'); ?></strong></td>
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
        <td><strong><?php echo ($module['result']) ? JText::_('Removed') : JText::_('Not removed'); ?></strong></td>
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
        <td><?php echo ucfirst($plugin['name']); ?></td>
        <td><?php echo ucfirst($plugin['group']); ?></td>
        <td><strong><?php echo ($plugin['result']) ? JText::_('Removed') : JText::_('Not removed'); ?></strong></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif;