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
    
    
    
    public function upload_design()
    {
        $config  = PFconfig::GetInstance();
        $quick   = (int) $config->Get('ajax_upload', 'design_review');
        $inc     = $this->GetInc("#__pf_designs");
        $file    = JRequest::getVar('file', NULL, 'files');
        $success = false;
        
        // Standard upload
        if(!$quick || $file) {
            $this->a_id    = $inc;
            $this->a_task  = "display_details";
            $this->a_title = $file['name'];
                
            $success = $this->Save();
        }
        else {
            // Ajax upload
            if(JRequest::getVar('qqfile', NULL, 'get')) {
                $file = JRequest::getVar('qqfile', NULL, 'get');
                $m = 'xhr';
            }
            else {
                $file = JRequest::getVar('qqfile', NULL, 'files');
                $m = 'form';
            }
            
            if($file) {
                $this->a_id    = $inc;
                $this->a_task  = "display_details";
                $this->a_title = ($m == 'xhr') ? $_GET['qqfile'] : $_FILES['qqfile']['name'];
                
                $success = $this->Save();
            }
        }
        
        return $success;
    }
    
    
    public function upload_revision()
    {
        $config  = PFconfig::GetInstance();
        $quick   = (int) $config->Get('ajax_upload', 'design_review');
        $inc     = $this->GetInc("#__pf_designs");
        $file    = JRequest::getVar('file', NULL, 'files');
        $success = false;
        
        $title = $this->GetTitle($this->a_id, '#__pf_designs');
        
        // Standard upload
        if(!$quick || $file) {
            $this->a_id    = $inc;
            $this->a_task  = "display_details";
            $this->a_title = $title.'::'.$file['name'];
                
            $success = $this->Save();
        }
        else {
            // Ajax upload
            if(JRequest::getVar('qqfile', NULL, 'get')) {
                $file = JRequest::getVar('qqfile', NULL, 'get');
                $m = 'xhr';
            }
            else {
                $file = JRequest::getVar('qqfile', NULL, 'files');
                $m = 'form';
            }
            
            if($file) {
                $this->a_id    = $inc;
                $this->a_task  = "display_details";
                $this->a_title = ($m == 'xhr') ? $title.'::'.$_GET['qqfile'] : $title.'::'.$_FILES['qqfile']['name'];
                
                $success = $this->Save();
            }
        }
        
        return $success;
    }
    
    
    public function task_delete_design()
    {
        $success = false;
        $rev     = (int) JRequest::getVar('rev', $this->a_id);
        
        foreach($this->cid AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_designs');
            
            $success = $this->Save();
        }
        
        if(!count($this->cid) && $rev) {
            
            $query = "SELECT rev_id FROM #__pf_designs WHERE id = '$rev'";
                   $this->db->setQuery($query);
                   $parent = (int) $this->db->loadResult();
            
            if($parent) {
                $this->a_title = $this->GetTitle($parent, '#__pf_designs').'::'.$this->GetTitle($rev, '#__pf_designs');
                $this->task    = 'task_delete_rev';
                $this->a_id    = 0;
            }
            else {
                $this->a_id    = 0;
                $this->a_title = $this->GetTitle($rev, '#__pf_designs');
            }
            
            
            $success = $this->Save();
        }
                    
        return $success;
    }
    
    
    public function task_save_comment()
    {
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_designs')." :: ".JRequest::getVar('title');
        $this->a_task  = "display_details";
        
        return $this->Save();
    }
    
    
    public function task_update()
    {
        $this->a_task  = "display_details";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_approve()
    {
        $this->a_task  = "display_details";
        $this->a_title = $this->GetTitle($this->a_id, '#__pf_designs');
        
        return $this->Save();
    }
    
    
    public function task_disapprove()
    {
        return $this->task_approve();
    }
}
?>