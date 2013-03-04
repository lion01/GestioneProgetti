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
    
    
    public function task_update()
    {
        $this->a_task  = "form_edit";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_save()
    {
        $this->a_id    = $this->GetInc("#__pf_groups");
        $this->a_task  = "form_edit";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_delete()
    {
        $success = false;
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_groups');
            
            $success = $this->Save();
        }
                    
        return $success;
    }
    
    
    public function task_copy()
    {
        return $this->task_delete();
    }
}
?>