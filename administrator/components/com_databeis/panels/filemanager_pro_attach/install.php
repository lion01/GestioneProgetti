<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$db = PFdatabase::GetInstance();

// Replace default file attachments panel if auto-enable
$auto_enable = (int) JRequest::getVar('auto_enable');

if($auto_enable) {
    $query = "UPDATE #__pf_panels SET enabled = '0' WHERE name = 'task_attachments'";
           $db->setQuery($query);
           $db->query();
         
}
?>