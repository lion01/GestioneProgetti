<?php
/**
* @package   Activity Stream
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$pf_core   = PFcore::GetInstance();
$pf_db     = PFdatabase::GetInstance();
$pf_user   = PFuser::GetInstance();
$pf_config = PFconfig::GetInstance();
$pf_lang   = PFlanguage::GetInstance();
$form      = new PFform('activitylog');

$use_section_filter = (int) $pf_config->Get('filter_section', 'cp_activitystream');
$use_user_filter    = (int) $pf_config->Get('filter_user', 'cp_activitystream');
$use_user_activity  = (int) $pf_config->Get('user_activity', 'cp_activitystream');
$delete_single      = (int) $pf_config->Get('delete_single', 'cp_activitystream');
$restrict_view      = (int) $pf_config->Get('restrict_view', 'cp_activitystream');

$limit  = (int) $pf_config->Get('limit', 'cp_activitystream');
$limit2 = (int) $pf_config->Get('limit2', 'cp_activitystream');

$f_section = JRequest::getVar('pflog_filter_section');
$f_user    = (int) JRequest::getVar('pflog_filter_user');
$workspace = $pf_user->GetWorkspace();
$projects  = $pf_user->Permission('projects');
$csection  = $pf_core->GetSection();

// Workspace filter
$projects_filter = "";

if($workspace) {
    $projects_filter = "\n WHERE l.project = '$workspace'";
}
else {
    if(count($projects)) {
        $projects = implode(',', $projects);
        $projects_filter = "\n WHERE l.project IN($projects)";
    }
}

// Section filter
$section_filter = "\n ";
if($f_section && in_array($f_section,$pf_user->Permission('sections')) && $use_section_filter) {
    $section_filter = "\n AND l.section = ".$pf_db->quote($f_section);
}

// user filter
$user_filter = "\n ";
if($f_user && in_array($f_user,$pf_user->Permission('userspace')) && $use_user_filter) {
    $user_filter = "\n AND l.user_id = ".$pf_db->quote($f_user);
}

if($projects_filter) {
    // query
    $query = "SELECT u.name, u.username, u.id AS uid, l.* FROM #__pf_log AS l"
           . "\n RIGHT JOIN #__users AS u ON u.id = l.user_id"
           . $projects_filter
           . $section_filter
           . $user_filter
           . "\n GROUP BY l.id ORDER BY cdate DESC LIMIT $limit";
           $pf_db->setQuery($query);
           $rows = $pf_db->loadObjectList();

    if(!is_array($rows)) $rows = array();
}
else {
    $rows = array();
}

if($pf_user->GetId() != 0) {

// Section filter
$section_list = array();
if($use_section_filter) {
    $sel_section = $f_section;
    $query = "SELECT name, title FROM #__pf_sections WHERE enabled = '1'"
           . "\n ORDER BY name ASC";
           $pf_db->setQuery($query);
           $section_list = $pf_db->loadObjectList();
}


// user filter
$user_list = array();
$userspace = implode(',', $pf_user->Permission('userspace'));
if($use_user_filter && $userspace != '') {
$sel_user = $f_user;
    $query = "SELECT id, name, username FROM #__users WHERE id IN(".$userspace.")"
           . "\n ORDER BY name ASC";
           $pf_db->setQuery($query);
           $user_list = $pf_db->loadObjectList();
}
echo $form->Start();

// Output section filter
if(is_array($section_list) && $use_section_filter) {
    echo "<select name='pflog_filter_section' onchange='this.form.submit();'>";
    echo "<option value='0'>".PFformat::Lang('PFL_LOG_FILTER_BY_SECTION')."</option>";
    foreach($section_list AS $f)
    {
        if(!in_array($f->name, $pf_user->Permission('sections'))) {
            continue;
        }
        $ps = "";

        if($sel_section == $f->name) { $ps = ' selected="selected"'; }
        echo "<option value='$f->name'$ps>".PFformat::Lang($f->title)."</option>";
    }
    echo "</select>&nbsp;";
}

// Output user filter
if(is_array($user_list) && $use_user_filter) {
    echo "<select name='pflog_filter_user' onchange='this.form.submit();'>";
    echo "<option value='0'>".PFformat::Lang('PFL_LOG_FILTER_BY_USER')."</option>";
    
    foreach($user_list AS $f)
    {
        $ps = "";

        if($sel_user == $f->id) { $ps = ' selected="selected"'; }
        echo "<option value='$f->id'$ps>".htmlspecialchars($f->name)." (".htmlspecialchars($f->username).")</option>";
    }
    echo "</select>&nbsp;";
}
// Clear log
if($pf_user->GetFlag() == 'system_administrator') {
    echo "<input type='button' class='button' value='".PFformat::Lang('PFL_LOG_CLEAR_LOG')."' onclick=\"this.form.clear_log.value = '1';this.form.submit();\" />";
}
echo "<br /><br /><strong>".PFformat::Lang('PFL_LOG_ACTIVITY_LOG')."</strong>";
echo "<ul class='activitylog'>";
$i = 0;

foreach($rows AS $row)
{
     JFilterOutput::objectHTMLSafe($row);

     if(!$pf_lang->TokenExists('PFL_LOG_'.strtoupper($row->section).'_'.strtoupper($row->task))) {
         continue;
     }
     
     if($pf_user->GetFlag() != 'system_administrator' && $restrict_view == 1) {
         if(!$pf_user->Access($row->task, $row->section)) {
             continue;
         }
     }

     $str = PFformat::Lang('PFL_LOG_'.strtoupper($row->section).'_'.strtoupper($row->task));
     
     // Replace name
     if($pf_user->Access('display_details', 'profile')) {
         $str = str_replace('{user}',
                            "<a href='".PFformat::Link("section=profile&id=$row->user_id")."'>"."$row->name ($row->username)"."</a>",
                            $str);
     }
     else {
         $str = str_replace('{user}', "$row->name ($row->username)", $str);
     }

     // replace link
     if($pf_user->Access($row->action_task, $row->action_section) && $row->action_id != 0) {
         $lws = "";
         if($row->project) {
             $lws = "&workspace=$row->project";
         }
         
         switch($row->task)
         {
             case 'task_save_folder':
                 $str = str_replace('{link}',
                 "<a href='".PFformat::Link("section=$row->action_section&task=$row->action_task&dir=$row->action_id".$lws)."'>".$row->action_title."</a>",
                 $str);
                 break;
                 
             case 'task_save_file':
                 $str = str_replace('{link}',
                 "<a href='".PFformat::Link("section=$row->action_section&dir=0&task=$row->action_task&id=$row->action_id".$lws)."'>".$row->action_title."</a>",
                 $str);
                 break;
                 
             default:
                $str = str_replace('{link}',
                "<a href='".PFformat::Link("section=$row->action_section&task=$row->action_task&id=$row->action_id".$lws)."'>".$row->action_title."</a>",
                $str);
                break;        
         }
     }
     else {
         $str = str_replace('{link}',$row->action_title,$str);
     }

     // delete single item link
     $delete_item = "";
     if($delete_single && $pf_user->GetFlag() == 'system_administrator') {
         $delete_item = "<a class=\"activity_delete\" href=\"".PFformat::Link("section=$csection&delete_item=$row->id")."\">[".PFformat::Lang('PFL_LOG_DELETE_ITEM')."]</a>";
     }
     
     echo "<li class='".$row->section."_".$row->task."'>";
     echo $str."&nbsp;$delete_item<span class='small activity_date'>".PFformat::ToDate($row->cdate)."</span>";
     echo "</li>";
     $i++;
}
if(!is_array($rows) || count($rows) <= 0 || $i == 0) {
    echo "<li>".PFformat::Lang('PFL_LOG_NO_ITEMS')."</li>";
}
echo "</ul>";
unset($rows);

if($use_user_activity) {
    $i = 0;
    $my_users = $pf_user->Permission('userspace');
    
    $query = "SELECT u.name, u.username, u.id AS uid, l.* FROM #__pf_log AS l"
           . "\n RIGHT JOIN #__users AS u ON u.id = l.user_id"
           . "\n WHERE action_task = 'setactivity'"
           . "\n GROUP BY l.id ORDER BY cdate DESC LIMIT $limit2";
           $pf_db->setQuery($query);
           $rows = $pf_db->loadObjectList();

    if(!is_array($rows)) $rows = array();

    if(count($rows)) {
        echo "<strong>".PFformat::Lang('PFL_LOG_USER_ACTIVITY_STATUS')."</strong>";
        echo "<ul>";
        foreach($rows AS $row)
        {
            if(!in_array($row->user_id,$my_users)) continue;

            JFilterOutput::objectHTMLSafe($row);
            
            $str = PFformat::Lang('PFL_LOG_'.strtoupper($row->action_section).'_'.strtoupper($row->action_task));

             // Replace name
             if($pf_user->Access('display_details', 'profile')) {
                 $str = str_replace('{user}',
                                    "<a href='".PFformat::Link("section=profile&id=$row->user_id")."' class='activity_user'><span>"."$row->name ($row->username)"."</span></a>",
                                    $str);
             }
             else {
                 $str = str_replace('{user}', "$row->name ($row->username)", $str);
             }

             // Replace link
             $str = str_replace('{link}',$row->action_title,$str);

             echo "<li class='".$row->action_section."_".$row->action_task."'>";
             echo $str."<span class='small activity_date'>".PFformat::ToDate($row->cdate)."</span>";
             echo "</li>";
             $i++;
        }
        if(!is_array($rows) || count($rows) <= 0 || $i == 0) {
            echo "<li>".PFformat::Lang('PFL_LOG_NO_ITEMS')."</li>";
        }
        echo "</ul>";
        unset($rows);
    }
}
$form->SetBind(true, 'REQUEST');
echo $form->HiddenField('option');
echo $form->HiddenField('section');
echo $form->HiddenField('task');
echo $form->HiddenField('clear_log');
echo $form->End();
}

unset($pf_core,$pf_db,$pf_user,$pf_config,$pf_lang,$form);
?>