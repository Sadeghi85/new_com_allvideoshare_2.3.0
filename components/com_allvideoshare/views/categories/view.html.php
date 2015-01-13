<?php

/*
 * @version		$Id: view.html.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import libraries
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_allvideoshare'.DS.'views'.DS.'view.php' );

class AllVideoShareViewCategories extends AllVideoShareView {

    function display($tpl = null) {
	    $mainframe = JFactory::getApplication();
		$model = $this->getModel();
				
		$config = $model->getconfig();
		$this->assignRef('config', $config);
		
		// Adds parameter handling
		$params = $mainframe->getParams();
		$this->assignRef('params',	$params);
		
		$rows = $params->get('no_of_rows', $config[0]->rows);
		$this->assignRef('rows', $rows);
		
		$cols = $params->get('no_of_cols', $config[0]->cols);
		$this->assignRef('cols', $cols);
		
		$thumb_width = $params->get('avs_thumb_width', $config[0]->thumb_width);
		$this->assignRef('thumb_width', $thumb_width);
		
		$thumb_height = $params->get('avs_thumb_height', $config[0]->thumb_height);
		$this->assignRef('thumb_height', $thumb_height);		
		
		if(substr(JVERSION,0,3) != '1.5') {
			$doc = JFactory::getDocument();
			if ($params->get('menu-meta_description')) {
				$doc->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords')) {
				$doc->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			if ($params->get('robots')) {
				$doc->setMetadata('robots', $params->get('robots'));
			}
		}
		
		$_categories = $model->getcategories($rows * $cols);
		$categories = array();
		foreach ($_categories as $_category) {
			// admins
			if (JFactory::getUser()->authorise('core.login.admin')) {
				$categories[] = $_category;
			}
			// registered
			elseif (JFactory::getUser()->authorise('core.login.site')) {
				if ($_category->access == 'registered' OR $_category->access == 'public') {
					$categories[] = $_category;
				}
			}
			// public
			else {
				if ($_category->access == 'public') {
					$categories[] = $_category;
				}
			}
		}
		$this->assignRef('categories', $categories);
		
		$pagination = $model->getpagination();
		$this->assignRef('pagination', $pagination);
				
        parent::display($tpl);
    }
	
}