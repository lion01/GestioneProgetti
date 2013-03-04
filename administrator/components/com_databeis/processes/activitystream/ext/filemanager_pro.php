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
    
    
    public function task_save_folder()
    {
        $this->a_title = '';
        $this->a_id    = $this->GetInc("#__pf_folders");
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_save_note()
    {
        $this->a_id    = $this->GetInc("#__pf_notes");
        $this->a_title = JRequest::getVar('title');
        $this->a_task  = "display_note";
        
        return $this->Save();
    }
    
    
    public function task_save_file()
    {
        $config  = PFconfig::GetInstance();
        $quick   = (int) $config->Get('quick_upload', 'filemanager_pro');
        $inc     = $this->GetInc("#__pf_files");
        $files   = JRequest::getVar('file', array(), 'files');
        $count   = (int) count($files['name']);
        $success = false;
        
        // Standard upload
        if(!$quick || $count) {
            $i = 0;

            while($count > $i)
            {
                $this->a_id    = $inc;
                $this->a_task  = "task_download";
                $this->a_title = $files['name'][$i];
                
                $success = $this->Save();
                
                $inc++;
                $i++;
            }
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
                $this->a_task  = "task_download";
                $this->a_title = ($m == 'xhr') ? $_GET['qqfile'] : $_FILES['qqfile']['name'];
                
                $success = $this->Save();
            }
        }
        
        return $success;
    }
    
    
    public function task_update_folder()
    {
        $atask  = '';
        $atitle = JRequest::getVar('title');
        return $this->Save();
    }
    
    
    public function task_update_note()
    {
        $this->a_task  = "display_note";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_update_file()
    {
        $this->a_task  = "task_download";
        $this->a_title = JRequest::getVar('title');
        
        return $this->Save();
    }
    
    
    public function task_delete()
    {
        $success = false;
        $folders = JRequest::getVar('folder', array());
        $notes   = JRequest::getVar('note', array());
        $files   = JRequest::getVar('file', array());
        
        foreach($folders AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_delete_folder";
            $this->a_title = $this->GetTitle($id, '#__pf_folders');
            
            $success = $this->Save();
        }
        
        foreach($notes AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_delete_note";
            $this->a_title = $this->GetTitle($id, '#__pf_notes');
            
            $success = $this->Save();
        }
        
        foreach($files AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_delete_file";
            $this->a_title = $this->GetTitle($id, '#__pf_files', 'name');
            
            $success = $this->Save();
        }
        
        return $success;
    }
    
    
    public function task_move()
    {
        $success = false;
        $folders = JRequest::getVar('folder', array());
        $notes   = JRequest::getVar('note', array());
        $files   = JRequest::getVar('file', array());
        
        foreach($folders AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_move_folder";
            $this->a_title = $this->GetTitle($id, '#__pf_folders');
            
            $success = $this->Save();
        }
        
        foreach($notes AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_move_note";
            $this->a_title = $this->GetTitle($id, '#__pf_notes');
            
            $success = $this->Save();
        }
        
        foreach($files AS $id)
        {
            $id = (int) $id;
            
            $this->a_id    = 0;
            $this->a_task  = "";
            $this->task    = "task_move_file";
            $this->a_title = $this->GetTitle($id, '#__pf_files', 'name');
            
            $success = $this->Save();
        }
        
        return $success;
    }
}
?>