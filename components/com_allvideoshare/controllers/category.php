<?php

/*
 * @version		$Id: category.php 2.3.0 2014-06-21 $
 * @package		Joomla
 * @copyright   Copyright (C) 2012-2014 MrVinoth
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import libraries
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_allvideoshare'.DS.'controllers'.DS.'controller.php' );

class AllVideoShareControllerCategory extends AllVideoShareController {

   function __construct() {
        parent::__construct();
    }
	
	function category()	{
	    $document = JFactory::getDocument();
		$vType = $document->getType();
		
	    $view = $this->getView('category', $vType);		
        $model = $this->getModel('category');		
        $view->setModel($model, true);
		
		$view->setLayout('default');
		$view->display();
	}
			
}