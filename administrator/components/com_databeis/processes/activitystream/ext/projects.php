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
        $this->a_task  = 'display_details';
        $this->a_title = JRequest::getVar('title');
        $this->a_id    = $this->GetInc("#__pf_projects");

        return $this->Save();
    }


    public function task_update()
    {
        $this->a_task  = 'display_details';
        $this->a_title = JRequest::getVar('title');

        return $this->Save();
    }


    public function task_request_join()
    {
        return $this->task_update();
    }


    public function task_delete()
    {
        $success = false;
        if(!count($this->cid)) return false;

        foreach($this->cid AS $id)
        {
            $id = (int) $id;

            $this->a_id    = 0;
            $this->a_title = $this->GetTitle($id, '#__pf_projects');

            $success = $this->Save();
        }

        return $success;
    }


    public function task_archive()
    {
        return $this->task_delete();
    }


    public function task_copy()
    {
        $success = false;
        if(!count($this->cid)) return false;

        foreach($this->cid AS $id)
        {
            $id = (int) $id;

            $this->a_id    = $id;
            $this->a_task  = 'display_details';
            $this->a_title = $this->GetTitle($id, '#__pf_projects');

            $success = $this->Save();
        }

        return $success;
    }


    public function task_activate()
    {
        return $this->task_archive();
    }


    public function task_approve()
    {
        return $this->task_copy();
    }


    public function accept_invite()
    {
        $iid = $this->db->Quote(JRequest::getVar('iid'));

        $query = "SELECT p.id, p.title FROM #__pf_project_invitations AS i"
               . "\n RIGHT JOIN #__pf_projects AS p ON p.id = i.project_id"
               . "\n WHERE i.inv_id = $iid";
               $this->db->setQuery($query);
               $o = $this->db->loadObject();

        if(!$o) return false;

        $this->a_task  = 'display_details';
        $this->a_title = $o->title;
        $this->a_id    = $o->id;

        return $this->Save();
    }


    public function decline_invite()
    {
        return $this->accept_invite();
    }
}
?>