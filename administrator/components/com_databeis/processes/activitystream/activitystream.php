<?php
/**
* @package   Activity Stream
* @copyright Copyright (C) 2009-2011 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

// Get core objects
$load = PFload::GetInstance();
$core = PFcore::GetInstance();
$user = PFuser::GetInstance();

// User must be logged in
if(!$user->GetId()) return false;

// Get the current section & task
$section = $core->GetSection();
$task    = $core->GetTask();
$uid     = $user->GetId();
$uname   = $user->GetName();
$ws      = $user->GetWorkspace();

// Get user input
$clear_log   = (int) JRequest::getVar('clear_log');
$setactivity = (int) JRequest::getVar('setactivity');
$delete_item = (int) JRequest::getVar('delete_item');
    
// Include the main activity stream
require_once($load->Process('activitystream.class.php', 'activitystream'));

// Request to clear the log?
if($clear_log && $user->GetFlag() == 'system_administrator') {
    $class = new PFactivityStream($section, $task, $uid, $uname, $ws);
    return $class->ClearLog();
}

// Request to delete single item?
if($delete_item && $user->GetFlag() == 'system_administrator') {
    $class = new PFactivityStream($section, $task, $uid, $uname, $ws);
    return $class->DeleteLogItem($delete_item);
}

// Someone changed his activity?
if($setactivity) {
    $class = new PFactivityStream($section, $task, $uid, $uname, $ws);
    return $class->SetActivity(JRequest::getVar('activity'));
}
unset($load, $core, $user);

// Include extension log class
$ext = dirname(__FILE__).DS.'ext'.DS.$section.'.php';
if(!file_exists($ext)) return false;

require_once($ext);

if(!class_exists('PFextLog')) return false;
$methods = get_class_methods('PFextLog');

if(in_array($task, $methods) && $task != '') {
    $log = new PFextLog($section, $task, $uid, $uname, $ws);
    return $log->$task();
}

return false;    
?>