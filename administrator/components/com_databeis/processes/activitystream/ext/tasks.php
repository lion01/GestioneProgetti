<?php
/**
* @package   Activity Stream
* @copyright Copyright (C) 2009-2011 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

class PFextLog extends PFactivityStream
{
    public function __construct($section, $task, $uid, $name, $ws = 0)
    {
        parent::__construct($section, $task, $uid, $name, $ws);
    }
    
    
    public function task_save_task()
    {
        $this->a_id    = $this->GetInc("#__pf_tasks");
        $this->a_task  = "display_details";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_save_milestone()
    {
        $this->a_id    = $this->GetInc("#__pf_milestones");
        $this->a_title = JRequest::getVar('title');
        $this->a_task  = "form_edit_milestone";
        
        return $this->Save();
    }
    
    
    public function task_update_task()
    {
        $this->a_task  = "display_details";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_update_milestone()
    {
        $this->a_title = JRequest::getVar('title');
        $this->a_task  = "form_edit_milestone";
        
        return $this->Save();
    }
    
    
    public function task_copy()
    {
        $success = false;
	    $mid     = JRequest::getVar('mid', array());
	    
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = $id;
            $this->a_task  = "display_details";
            $this->a_title = $this->GetTitle($id, '#__pf_tasks');
            
            $success = $this->Save();
        }
        
        foreach($mid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = $id;
            $this->a_task  = "";
            $this->task    = "task_copy_ms";
            $this->a_title = $this->GetTitle($id, '#__pf_milestones');
            
            $success = $this->Save();
        }
        
        return $success;
    }
    
    
    public function task_delete()
    {
        $success = false;
        $mid     = JRequest::getVar('mid', array());
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_tasks');
            
            $success = $this->Save();
        }
        
        foreach($mid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_delete_ms";
            $this->a_title = $this->GetTitle($id, '#__pf_milestones');
            
            $success = $this->Save();
        }
        
        return $success;
    }
    
    
    public function task_reorder()
    {
        return $this->Save();
    }
    
    
    public function task_save_comment()
    {
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_tasks')." :: ".JRequest::getVar('title');
        $this->a_task  = "display_details";
        
        return $this->Save();
    }
    
    
    public function task_update_comment()
    {
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_tasks')." :: ".JRequest::getVar('title');
        $this->a_task  = "display_details";
        
        return $this->Save();
    }
    
    
    public function task_delete_comment()
    {
        $this->a_id = 0;
        $this->a_title = $this->GetTitle($id, '#__pf_tasks');
        $this->a_task  = "display_details";
        
        return $this->Save();
    }
    
    
    public function task_update_progress()
    {
        $progress = JRequest::getVar('progress', array(), 'array');
        $progress = (int) $progress[$this->a_id];
        
        $this->a_title = $this->GetTitle($id, '#__pf_tasks')." ($progress %)";
        $this->a_task  = "display_details";
        
        return $this->Save();
    }
}
?>