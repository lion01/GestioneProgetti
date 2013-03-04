<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


$load   = PFload::GetInstance();
$db     = PFdatabase::GetInstance();
$config = PFconfig::GetInstance();

require_once($load->Section('filemanager_pro.class.php', 'filemanager_pro'));
$class = new PFfilemanagerClass();

// Convert all notes
$query = "SELECT * FROM #__pf_notes";
       $db->setQuery($query);
       $rows = $db->loadObjectList();

if(!is_array($rows)) $rows = array();

foreach($rows AS $row)
{
    // Add properties
    $query = "INSERT INTO #__pf_note_properties VALUES(NULL, '$row->id', '0', '0', '0', '0', '0', '')";
		   $db->setQuery($query);
		   $db->query();

   // Add version
   $class->CreateNoteVersion($row->id,$row->title,$row->description,$row->content,$row->author,$row->cdate);
}

// Convert all files
$query = "SELECT * FROM #__pf_files";
       $db->setQuery($query);
       $rows = $db->loadObjectList();

if(!is_array($rows)) $rows = array();

foreach($rows AS $row)
{
    // Add properties
    $query = "INSERT INTO #__pf_file_properties VALUES("
           . "\n NULL, '$row->id', '0', '0', '0', '0', '0', ''"
           . "\n )";
		   $db->setQuery($query);
		   $db->query();

   // Add version
   $class->CreateFileVersion($row->id,$row->name,$row->prefix,$row->description,$row->author,$row->filesize,$row->cdate);
}

// Set upload path
$path = $config->Get('upload_path', 'filemanager');
$config->Set('upload_path', $path, 'filemanager_pro');

// Replace default file manager if auto-publish
$auto_enable = (int) JRequest::getVar('auto_enable');

if($auto_enable) {
    $query = "SELECT ordering FROM #__pf_sections WHERE name = 'filemanager'";
           $db->setQuery($query);
           $order = (int) $db->loadResult();
           
    $query = "SELECT is_default FROM #__pf_sections WHERE name = 'filemanager'";
           $db->setQuery($query);
           $is_default = (int) $db->loadResult();
           
    if($order) {
        $query = "UPDATE #__pf_sections SET ordering = '$order' WHERE name = 'filemanager_pro'";
               $db->setQuery($query);
               $db->query();
    }
    
    if($is_default) {
        $query = "UPDATE #__pf_sections SET is_default = '1' WHERE name = 'filemanager_pro'";
               $db->setQuery($query);
               $db->query();
               
        $query = "UPDATE #__pf_sections SET is_default = '0' WHERE name = 'filemanager'";
               $db->setQuery($query);
               $db->query();       
    }
    
    $query = "UPDATE #__pf_sections SET enabled = '0' WHERE name = 'filemanager'";
               $db->setQuery($query);
               $db->query();
}
?>