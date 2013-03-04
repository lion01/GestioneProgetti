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
    
    public function task_save()
    {
        $this->a_id      = $this->GetInc("#__users");
        $this->a_section = "profile";
        $this->a_task    = "display_details";
        $this->a_title   = JRequest::getVar('name');
        
        return $this->Save();
    }
    
    
    public function task_save_accesslvl()
    {
        $this->a_id    = $this->GetInc("#__pf_access_levels");
        $this->a_task  = "form_edit";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_update()
    {
        $this->a_section = "profile";
        $this->a_task    = "display_details";
        $this->a_title   = JRequest::getVar('name');
        
        return $this->Save();
    }
    
    
    public function task_update_accesslvl()
    {
        $this->a_task  = "form_edit_accesslvl";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_delete()
    {
        $success = false;
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id = 0;
            
            $query = "SELECT name FROM #__users WHERE id = '$id'";
                   $this->db->setQuery($query);
                   $this->a_title = $this->db->loadResult();
                   
            $success = $this->Save();
        }
        
        return $success;
    }
    
    
    public function task_delete_accesslvl()
    {
        $success = false;
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_access_levels');
            
            $success = $this->Save();
        }
        
        return $success;
    }
}
?>