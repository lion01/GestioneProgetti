<?php
/**
* @package   Activity Stream
* @copyright Copyright (C) 2009-2011 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

class PFactivityStream
{
    protected $db;
    protected $section;
    protected $task;
    protected $uid;
    protected $name;
    protected $ws;
    protected $cid;
    protected $a_section;
    protected $a_task;
    protected $a_title;
    protected $a_id;

    public function __construct($section, $task, $uid, $name, $ws = 0)
    {
        $this->db = PFdatabase::GetInstance();

        $this->section = $section;
        $this->task    = $task;
        $this->uid     = $uid;
        $this->ws      = $ws;
        $this->name    = $name;
        $this->cid     = JRequest::getVar('cid', array());

        $this->a_section = $this->section;
        $this->a_task    = NULL;
        $this->a_title   = NULL;
        $this->a_id      = JRequest::getVar('id');
        $this->a_id      = explode(':', $this->a_id);
        $this->a_id      = (int) $this->a_id[0];
    }


    public function ClearLog()
    {
        $query = "TRUNCATE TABLE #__pf_log";
               $this->db->setQuery($query);
               $this->db->query();

        if($this->db->getErrorMsg()) return false;
        return true;
    }


    public function DeleteLogItem($id)
    {
        $id = $this->db->Quote($id);
        $query = "DELETE FROM #__pf_log WHERE id = $id";
               $this->db->setQuery($query);
               $this->db->query();

        if($this->db->getErrorMsg()) return false;
        return true;
    }


    public function SetActivity($activity = '')
    {
        $this->a_title = $activity;
        $this->a_task  = "setactivity";
        $this->a_id    = $this->GetInc("#__pf_projects");

        return $this->Save();
    }


    protected function GetInc($table)
    {
        $db = JFactory::getDBO();

        $jversion = new JVersion();
        $v = $jversion->RELEASE;

        if($v == '1.5' || $v == '1.6') {
            $table = $db->replacePrefix($table);
        }
        else {
            $cfg   = JFactory::getConfig();
            $table = str_replace("#__", $cfg->get('dbprefix'), $table);
        }

        $query = "SHOW TABLE STATUS LIKE '".$table."'";
		       $db->setQuery($query);
		       $result = $db->loadAssocList();

	    $id = $result[0]['Auto_increment'];

        unset($db);
        return $id;
    }


    protected function GetTitle($id, $table, $field = 'title')
    {
        $query = "SELECT $field FROM $table WHERE id = $id";
               $this->db->setQuery($query);
               $title = $this->db->loadResult();

        return htmlspecialchars( $title );
    }


    protected function Save()
    {
        $time = time();

        $s = $this->db->Quote($this->section);
        $t = $this->db->Quote($this->task);
        $p = $this->db->Quote($this->ws);

        $a_s  = $this->db->Quote($this->a_section);
        $a_ta = $this->db->Quote($this->a_task);
        $a_ti = $this->db->Quote($this->a_title);
        $a_id = $this->db->Quote($this->a_id);

        $uid = $this->db->Quote($this->uid);

        $query = "INSERT INTO #__pf_log VALUES("
               . "\n NULL, $uid, $p, $s, $t,"
               . "\n $a_s, $a_ta, $a_id, $a_ti, $time)";
               $this->db->setQuery($query);
               $this->db->query();

        if($this->db->getErrorMsg()) return false;
        return true;
    }
}
?>