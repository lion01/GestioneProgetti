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
    
    
    public function task_save_topic()
    {
        $this->a_id    = $this->GetInc("#__pf_topics");
        $this->a_task  = "display_details";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_save_reply()
    {
        $this->a_task  = "display_details";
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_topics')." :: ".JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_update_topic()
    {
        $this->a_task  = "display_details";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_update_reply()
    {
        $this->a_task  = "display_details";
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_topics')." :: ".JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_delete_topic()
    {
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_topics');
            
            return $this->Save();
        }
    }
    
    
    public function task_delete_reply()
    {
        $rid = (int) JRequest::getVar('rid');
        
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_topics')." ::".$this->GetTitle($rid, '#__pf_topic_replies');
        $this->a_id    = 0;
        
        return $this->Save();
    }
    
    
    public function task_subscribe()
    {
        $success = false;
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = $id;
            $this->a_task  = "display_details";
            $this->a_title = $this->GetTitle($id, '#__pf_topics');
            
            $success = $this->Save();
        }
        
        return $success;
    }
    
    
    public function task_unsubscribe()
    {
        return $this->task_subscribe();
    }
}
?>