<?php
/**
* @package   Hide Buttons
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$doc = JFactory::getDocument();

$doc->addStyleDeclaration("li.btn a.pf_nav_gray { display:none !important; }");
?>