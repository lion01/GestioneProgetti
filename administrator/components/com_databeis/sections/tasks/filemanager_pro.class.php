<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


class PFfilemanagerClass extends PFobject
{
    public function __construct()
    {
        parent::__construct();
    }

    public function CompareVersions($content1, $content2)
    {
        $content1 = preg_replace('/\r\n|\r/', "\n", $content1);
        $content2 = preg_replace('/\r\n|\r/', "\n", $content2);
        $content1 = str_replace("<br/>", "\n", $content1);
        $content1 = str_replace("<br />", "\n", $content1);
        $content1 = str_replace("</p>", "\n", $content1);
        $content1 = str_replace("</li>", "\n", $content1);
        $content2 = str_replace("<br/>", "\n", $content2);
        $content2 = str_replace("<br />", "\n", $content2);
        $content2 = str_replace("</p>", "\n", $content2);
        $content2 = str_replace("</li>", "\n", $content2);
        $content1 = strip_tags($content1);
        $content2 = strip_tags($content2);

        $tmp_content1 = explode("\n", $content1);
        $tmp_content2 = explode("\n", $content2);

        $content1 = array();
        $content2 = array();
        $missing  = array();
        $new      = array();

        foreach ($tmp_content1 AS $c)
	    {
		    $c = trim($c);
		    $content1[] = $c;
	    }

        foreach ($tmp_content2 AS $c)
	    {
		    $c = trim($c);
		    $content2[] = $c;
	    }

        // find missing content in note 1
        foreach ($content1 AS $i => $content)
        {
	        $found = false;

	        if(in_array($content, $content2)) {
		        $found = true;
	        }

            if(!$found) {
    	        $missing[] = $i;
            }
        }

        // find new content in note 2
        foreach ($content2 AS $i => $content)
        {
	        $found = false;

            if(in_array($content, $content1)) {
		        $found = true;
	        }

            if(!$found) {
    	        $new[] = $i;
            }
        }

        return array($content1,$content2,$missing,$new);
    }

	public function LoadFolderList($dir = 0, $ob = 'id', $od = 'ASC', $keyword = '', $workspace = 0)
	{
	    $db   = PFdatabase::GetInstance();
	    $user = PFuser::GetInstance();
	    $rows = array();

	    $flag     = $user->GetFlag();
	    $groups   = $user->Permission('groups');
	    $projects = $user->Permission('projects');

	    // Setup filter
		$filter = " \n AND t.parent_id = ".$db->quote($dir);
		if($keyword) {
            $filter .= "\n AND (f.title LIKE ".$db->quote("%$keyword%")
                    .  "\n OR f.description LIKE ".$db->quote("%$keyword%").")";
		}

		// We have a workspace
		if($workspace) {
            $query = "SELECT f.*, u.name, u.id AS uid, a.folder_id AS restricted FROM #__pf_folders AS f"
    		       . "\n RIGHT JOIN #__pf_folder_tree AS t ON t.folder_id = f.id"
    		       . "\n LEFT JOIN #__users AS u ON u.id = f.author"
    		       . "\n LEFT JOIN #__pf_folder_access AS a ON a.folder_id = f.id"
    		       . "\n WHERE f.project = '$workspace'"
    		       . $filter
    		       . "\n GROUP BY f.id"
    		       . "\n ORDER BY f.$ob $od";
    		       $db->setQuery($query);
    		       $tmp_rows = $db->loadObjectList();

            if(!is_array($tmp_rows)) $tmp_rows = array();

    		// Check access
    		if($flag == 'system_administrator' || $flag == 'project_administrator') {
                $rows = $tmp_rows;
            }
            else {
                foreach($tmp_rows AS $row)
                {
                    if($row->restricted) {
                        if(!$this->CheckFolderAccess($row)) continue;
                    }
                    $rows[] = $row;
                }
            }
        }
        else {
            // Load project "root" folders
            $projects_num = count($projects);

            if($projects_num) {
                $projects = implode(',',$projects);
                $query = "SELECT p.id, p.title, p.cdate, u.name, u.id AS uid FROM #__pf_projects AS p"
                       . "\n LEFT JOIN #__users AS u ON u.id = p.author"
                       . "\n WHERE p.id IN($projects)"
                       . "\n GROUP BY p.id"
                       . "\n ORDER BY p.title ASC";
                       $db->setQuery($query);
    		           $rows = $db->loadObjectList();

    		     if(!is_array($rows)) $rows = array();
            }
            else {
                $rows = array();
            }
        }

		return $rows;
	}

	public function LoadFolder($id)
	{
	    static $rows = array();

	    if(in_array($id, $rows)) return $rows[$id];

	    $db = PFdatabase::GetInstance();

		$query = "SELECT * FROM #__pf_folders"
               . "\n WHERE id = ".$db->Quote($id);
		       $db->setQuery($query);
		       $row = $db->loadObject();

		if(!is_null($row)) {
			$query = "SELECT task_id FROM #__pf_task_attachments"
                   . "\n WHERE attach_id = '$id' AND attach_type = 'folder'";
			       $db->setQuery($query);
			       $row->attachments = $db->loadResultArray();

			$row->groups = $this->LoadFolderAccess($id);
		}

        $rows[$id] = $row;
		return $row;
	}

	public function LoadParentFolderId($id)
	{
        $db = PFdatabase::GetInstance();

		$query = "SELECT parent_id FROM #__pf_folder_tree"
               . "\n WHERE folder_id = ".$db->Quote($id);
		       $db->setQuery($query);
		       $parent_id = (int) $db->loadResult();

		return $parent_id;
    }

    public function LoadFolderAccess($id)
    {
        $db = PFdatabase::GetInstance();

        $query = "SELECT group_id FROM #__pf_folder_access"
               . "\n WHERE folder_id = '$id'";
               $db->setQuery($query);
               $groups = $db->loadResultArray();

        if(!is_array($groups)) $groups = array();

        return $groups;
    }

	public function LoadNoteList($dir = 0, $ob = 'id', $od = 'ASC', $keyword = '', $workspace = 0)
	{
	    $db = PFdatabase::GetInstance();

	    // Setup filter
		$filter = "";
		if($keyword) {
			$filter .= "\n AND (n.title LIKE ".$db->quote("%$keyword%")
                    .  "\n OR n.description LIKE ".$db->quote("%$keyword%").")";
		}

		$query = "SELECT n.*, u.name, u.id AS uid, p.checked_out, "
               . "\n p.checked_out_user, p.locked, p.locked_user, p.status, COUNT(v.id) AS version"
		       . "\n FROM #__pf_notes AS n"
		       . "\n LEFT JOIN #__users AS u ON u.id = n.author"
		       . "\n LEFT JOIN #__pf_note_properties AS p ON p.note_id = n.id"
		       . "\n LEFT JOIN #__pf_note_versions AS v ON v.note_id = n.id"
		       . "\n WHERE n.dir = ".$db->quote($dir)
		       . "\n AND n.project = '$workspace'"
		       . $filter
		       . "\n GROUP BY n.id"
		       . "\n ORDER BY n.$ob $od";
		       $db->setQuery($query);
		       $rows = $db->loadObjectList();

		if(!is_array($rows)) $rows = array();

		return $rows;
	}

	public function LoadNote($id)
	{
	    $db = PFdatabase::GetInstance();

		$query = "SELECT n.*, p.checked_out, p.checked_out_user FROM #__pf_notes AS n"
		       . "\n LEFT JOIN #__pf_note_properties AS p ON p.note_id = n.id"
               . "\n WHERE n.id = '$id'";
		       $db->setQuery($query);
		       $row = $db->loadObject();

		if(!is_null($row)) {
			$query = "SELECT task_id FROM #__pf_task_attachments"
                   . "\n WHERE attach_id = '$id'"
                   . "\n AND attach_type = 'note'";
			       $db->setQuery($query);
			       $row->attachments = $db->loadResultArray();
		}

		return $row;
	}

    public function LoadNoteVersion($id)
    {
        $db = PFdatabase::GetInstance();

        $query = "SELECT n.*, u.name FROM #__pf_note_versions AS n"
		       . "\n LEFT JOIN #__users AS u ON u.id = n.author"
		       . "\n WHERE n.id = '$id'";
               $db->setQuery($query);
               $row = $db->loadObject();

        return $row;
    }

	public function LoadNoteVersions($id)
	{
	    $db = PFdatabase::GetInstance();

		$query = "SELECT n.*, u.name FROM #__pf_note_versions AS n"
		       . "\n LEFT JOIN #__users AS u ON u.id = n.author"
		       . "\n WHERE n.note_id = '$id'"
		       . "\n GROUP BY n.id"
		       . "\n ORDER BY n.id DESC";
		       $db->setQuery($query);
		       $rows = $db->loadObjectList();

		if(!is_array($rows)) $rows = array();

		return $rows;
	}

    public function LoadFileVersions($id)
	{
	    $db = PFdatabase::GetInstance();

		$query = "SELECT f.*, u.name AS authorname FROM #__pf_file_versions AS f"
		       . "\n LEFT JOIN #__users AS u ON u.id = f.author"
		       . "\n WHERE f.file_id = '$id'"
		       . "\n GROUP BY f.id"
		       . "\n ORDER BY f.id DESC";
		       $db->setQuery($query);
		       $rows = $db->loadObjectList();

		if(!is_array($rows)) $rows = array();

		return $rows;
	}

	public function LoadFileList($dir = 0, $ob = 'id', $od = 'ASC', $keyword = '', $workspace = 0)
	{
	    $db = PFdatabase::GetInstance();

	    // Setup filter
		$filter = "";
		if($keyword) {
			$filter .= "\n AND (f.name LIKE ".$db->quote("%$keyword%")
                    .  "\n OR f.description LIKE ".$db->quote("%$keyword%").")";
		}

		if($ob == 'title') $ob = "name";

		$query = "SELECT f.*, u.name AS uname, u.id AS uid, p.checked_out,"
               . "\n p.checked_out_user, p.locked, p.locked_user, p.status, COUNT(v.id) AS version"
               . "\n FROM #__pf_files AS f"
		       . "\n LEFT JOIN #__users AS u ON u.id = f.author"
               . "\n LEFT JOIN #__pf_file_properties AS p ON p.file_id = f.id"
               . "\n LEFT JOIN #__pf_file_versions AS v ON v.file_id = f.id"
		       . "\n WHERE f.dir = ".$db->quote($dir)
		       . "\n AND f.project = '$workspace'"
		       . $filter
               . "\n GROUP BY f.id"
		       . "\n ORDER BY f.$ob $od";
		       $db->setQuery($query);
		       $rows = $db->loadObjectList();

		if(!is_array($rows)) $rows = array();

		return $rows;
	}

	public function LoadFile($id, $v = 0)
	{
	    $db = PFdatabase::GetInstance();

        if($v) {
            $query = "SELECT v.*,f.project FROM #__pf_file_versions AS v"
                   . "\n RIGHT JOIN #__pf_files AS f ON f.id = v.file_id"
                   . "\n WHERE v.id = ".$db->quote($v)
                   . "\n AND v.file_id = ".$db->quote($id);
        }
        else {
            $query = "SELECT f.*,p.checked_out,p.checked_out_user FROM #__pf_files AS f"
                   . "\n LEFT JOIN #__pf_file_properties AS p ON p.file_id = f.id"
                   . "\n WHERE f.id = ".$db->quote($id);
        }

		$db->setQuery($query);
		$row = $db->loadObject();

		if(!is_null($row)) {
			$query = "SELECT task_id FROM #__pf_task_attachments"
                   . "\n WHERE attach_id = '$id' AND attach_type = 'file'";
			       $db->setQuery($query);
			       $row->attachments = $db->loadResultArray();

			if(!is_array($row->attachments)) $row->attachments = array();
		}

		return $row;
	}

	public function SaveFolder($dir)
	{
	    $db     = PFdatabase::GetInstance();
	    $user   = PFuser::GetInstance();
	    $config = PFconfig::GetInstance();

		$title = trim(JRequest::getVar('title'));

		if(!$title) {
            $this->AddError('V_TITLE');
            return false;
        }

        $title   = $db->Quote($title);
		$desc    = $db->Quote(JRequest::getVar('description'));
		$tasks   = JRequest::getVar('tasks', array(), 'array');
		$groups  = JRequest::getVar('groups', array(), 'array');
		$project = $user->GetWorkspace();
		$uid     = $user->GetId();
		$now     = time();

		$query = "INSERT INTO #__pf_folders VALUES("
               . "\n NULL, $title, $desc, $uid, $project, $now, $now"
               . "\n )";
		         $db->setQuery($query);
		         $db->query();

        if($db->getErrorMsg()) $this->AddError($db->getErrorMsg());

		$id = $db->insertid();
		if(!$id) return false;

		$query = "INSERT INTO #__pf_folder_tree VALUES("
               . "\n NULL, $id, $dir"
               . "\n )";
			   $db->setQuery($query);
			   $db->query();

        $data = array($id);
        PFprocess::Event('save_note', $data);

		// Save task connections
		if((int) $config->Get('attach_folders', 'filemanager_pro')) {
		    $this->SaveAttachments($id, 'folder', $tasks);
		}

		// Save folder access
		if($user->Access('restrict_folder', 'filemanager_pro')) {
            if(!$this->SaveFolderAccess($id, $dir, $groups)) return false;
        }

		return true;
	}

	public function SaveNote($dir = 0)
	{
	    $db     = PFdatabase::GetInstance();
	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();

		$title   = trim(JRequest::getVar('title'));
		$desc    = $db->Quote(JRequest::getVar('description'));
		$tasks   = JRequest::getVar('tasks', array(), 'array');
		$project = $user->GetWorkspace();
		$uid     = $user->GetId();
		$now     = time();

		if(defined('PF_DEMO_MODE')) {
			$content = $db->Quote(JRequest::getVar('text'));
		}
		else {
			$content = $db->Quote(JRequest::getVar('text', '', 'default', 'none', JREQUEST_ALLOWRAW));
		}

		if(!$title) {
            $this->AddError('V_TITLE');
            return false;
        }

        $title = $db->Quote($title);

		$query = "INSERT INTO #__pf_notes VALUES ("
               . "\n NULL, $title, $desc, $content, $uid, $project,"
		       . "\n $dir, $now, $now"
               . "\n )";
		       $db->setQuery($query);
		       $db->query();
		       $id = $db->insertid();

        if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

		if(!$id) return false;

		// Save task connections
		if((int) $config->Get('attach_notes', 'filemanager_pro')) {
		    $this->SaveAttachments($id, 'note', $tasks);
		}

		// Add properties
		$query = "INSERT INTO #__pf_note_properties VALUES("
               . "\n NULL, '$id', '0', '0', '0', '0', '0', ''"
               . "\n )";
		       $db->setQuery($query);
		       $db->query();

		// Create new version
		if($config->Get('note_vc', 'filemanager_pro') == '1') {
		    $title = JRequest::getVar('title');
		    $desc  = JRequest::getVar('description');
		    $cntnt = (defined('PF_DEMO_MODE')) ? JRequest::getVar('text') : JRequest::getVar('text', '', 'default', 'none', JREQUEST_ALLOWRAW);

            $this->CreateNoteVersion($id, $title,$desc,$cntnt,$uid,$now);
		}

		return true;
	}

	public function UploadFiles($files, $dir = 0)
	{
		jimport('joomla.filesystem.file');
        require_once($this->GetHelper('filemanager_pro'));

		$user   = PFuser::GetInstance();
		$config = PFconfig::GetInstance();
		$db     = PFdatabase::GetInstance();

		$descs   = JRequest::getVar('description', array());
		$tasks   = JRequest::getVar('tasks', array(), 'array');
		$project = $user->GetWorkspace();
		$uid     = $user->GetId();
		$count   = (int) count($files['name']);
		$e_data  = array();
		$i       = 0;

		$upath = PFfilemanagerHelper::GetUploadPath();

		$upath_root = $upath;
        if($project) $upath = $upath.DS.'project_'.$project;

        // Make sure upload dir exists
    	if(!is_dir($upath)) {
            JFolder::create($upath);
            JPath::setPermissions($upath, '0644', '0755');
        }
        else {
            // Chmod upload root folder
		    JPath::setPermissions($upath_root, '0644', '0755');
        }

        // Make sure upload dir index.html exists
        $index   = $upath.DS.'index.html';
        $content = '<html><body bgcolor="#FFFFFF"></body></html>';
        if(!file_exists($index)) JFile::write($index, $content);

        // Make sure upload root dir index.html exists
        $index = $upath_root.DS.'index.html';
        if(!file_exists($index)) JFile::write($index, $content);

		while ($count > $i)
		{
			$file             = array();
			$file['size']     = $files['size'][$i];
			$file['tmp_name'] = $files['tmp_name'][$i];
			$file['name']     = JFile::makeSafe($files['name'][$i]);			PFformat::Logging($file['name']);
			PFformat::Logging($file['tmp_name']);
            $desc = $descs[$i];
			$now  = time();
            $e    = false;

			if(isset($file['name'])) {
				// Generate prefix
				$prefix2  = uniqid(md5($file['name']).rand(1,1000))."_";
				$filepath = $upath;
				$size     = $file['size'] / 1024;
				$name     = JFile::makeSafe($file['name']);
                $dest     = $filepath.DS.$prefix2.strtolower(JFile::makeSafe($file['name']));

				// upload the file
				if (!JFile::upload($file['tmp_name'], $dest)) {
				    $this->AddError('MSG_FILE_E_UPLOAD');
					$e = true;
                    $i++;
					continue;
				}

                if((int)$config->Get('file_vc', 'filemanager_pro')) {
                    // Search for existing file name
                    $v_id = PFfilemanagerHelper::FileNameExists($name, $dir);

                    if($v_id) {
                        $v_id2 = $this->CreateFileVersion($v_id, $name, $prefix2, '', $user->GetId(), $size, time());
                        if($v_id2) {
                            // Add properties
                            $query = "INSERT INTO #__pf_file_properties VALUES("
                                   . "\n NULL, '$v_id2', '0', '0', '0', '0', '0', ''"
                                   . "\n )";
            	                   $db->setQuery($query);
            	                   $db->query();

                            $id = $db->insertid();
    			            if(!$id) {
    			                $this->AddError($db->getErrorMsg());
                                $e = true;
                                $i++;
					            continue;
    			            }

                            // Update main record
                            $q_name = $db->quote($name);
                            $q_size = $db->quote($size);
                            $q_pfx  = $db->quote($prefix);

                            $query = "UPDATE #__pf_files SET name = $q_name description = '',"
                                   . "\n filesize = $q_size, edate = ".$db->quote(time()).", "
		                           . "\n prefix = $q_pfx WHERE id = ".$db->quote($v_id2);
		                           $db->setQuery($query);
		                           $db->query();

                            $id = $db->insertid();
    			            if(!$id) {
    			                $this->AddError($db->getErrorMsg());
                                $e = true;
                                $i++;
					            continue;
    			            }
                        }

                        $i++;
					    continue;
                    }
                }

                // Insert db record
				$query = "INSERT INTO #__pf_files VALUES("
                       . "\n NULL, ".$db->quote($name).", '".$prefix2."',"
                       . "\n ".$db->quote($desc).", ".$db->quote($uid).","
				       . "\n ".$db->quote($project).", ".$db->quote($dir).","
                       . "\n ".$db->quote($size).", ".$db->quote($now).", "
                       . "\n ".$db->quote($now).")";
				       $db->setQuery($query);
				       $db->query();

				$id = $db->insertid();

				if(!$id) {
					$this->AddError($this->_db->getErrorMsg());
                    $e = true;
                    $i++;
					continue;
				}

				// Save task connections
		        if((int) $config->Get('attach_files', 'filemanager_pro')) {
				    $this->SaveAttachments($id, 'file', $tasks);
		        }

                // Add properties
		        $query = "INSERT INTO #__pf_file_properties VALUES("
                       . "\n NULL, '$id', '0', '0', '0', '0', '0', ''"
                       . "\n )";
		               $db->setQuery($query);
		               $db->query();

                // Create new version
		        if((int)$config->Get('file_vc', 'filemanager_pro')) {
			        $this->CreateFileVersion($id,$name,$prefix2,$desc,$uid,$size,$now);
		        }
		        $e_data[] = $id;
			}
			$i++;
		}

		if($e) return false;

        PFprocess::Event('save_file', $e_data);
		return true;
	}

	public function UpdateFolder($id)
	{
	    $db     = PFdatabase::GetInstance();
	    $user   = PFuser::GetInstance();
	    $config = PFconfig::GetInstance();

		$title   = trim(JRequest::getVar('title'));
		$desc    = $db->Quote(JRequest::getVar('description'));
		$tasks   = JRequest::getVar('tasks', array(), 'array');
		$groups  = JRequest::getVar('groups', array(), 'array');
		$groups2 = JRequest::getVar('oldgroups', array(), 'array');
		$now     = time();

		if(!$title) {
            $this->AddError('V_TITLE');
            return false;
        }

        $title = $db->Quote($title);

		$query = "UPDATE #__pf_folders SET title = $title, description = $desc,"
		       . "\n edate = '$now' WHERE id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

		// Update task connections
		if((int) $config->Get('attach_folders', 'filemanager_pro')) {
			$this->UpdateAttachments($id, 'folder', $tasks);
		}

		// Save folder access
		$dir = (int) JRequest::getVar('dir');

		if($user->Access('restrict_folder', 'filemanager_pro')) {
            if(!$this->UpdateFolderAccess($id, $dir, $groups, $groups2)) return false;
        }

        $data = array($id);
        PFprocess::Event('update_folder', $data);

		return true;
	}

	public function UpdateNote($id)
	{
	    $db     = PFdatabase::GetInstance();
	    $user   = PFuser::GetInstance();
	    $config = PFconfig::GetInstance();

		$title = trim(JRequest::getVar('title'));
		$desc  = $db->Quote(JRequest::getVar('description'));
		$tasks = JRequest::getVar('tasks', array(), 'array');
		$uid   = $user->GetId();
		$now   = time();

		if(defined('PF_DEMO_MODE')) {
			$content = $db->Quote(JRequest::getVar('text'));
		}
		else {
			$content = $db->Quote(JRequest::getVar('text', '', 'default', 'none', JREQUEST_ALLOWRAW));
		}

		if(!$title) {
            $this->AddError('V_TITLE');
            return false;
        }

        $title = $db->Quote($title);

		$query = "UPDATE #__pf_notes SET title = $title, description = $desc,"
		       . "\n content = $content, edate = $now"
               . " \n WHERE id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
		    $this->AddError($db->getErrorMsg());
			return false;
		}

		// update task connections
		if((int) $config->Get('attach_notes', 'filemanager_pro')) {
			$this->UpdateAttachments($id, 'note', $tasks);
		}

	    // Create new version
		if($config->Get('note_vc', 'filemanager_pro')) {
		    $title = trim(JRequest::getVar('title'));
		    $desc  = JRequest::getVar('description');
		    $cntnt = (defined('PF_DEMO_MODE')) ? JRequest::getVar('text') : JRequest::getVar('text', '', 'default', 'none', JREQUEST_ALLOWRAW);

			$this->CreateNoteVersion($id, $title,$desc,$cntnt,$uid,$now);
		}

        $data = array($id);
        PFprocess::Event('update_note', $data);

		return true;
	}

	public function UpdateFile($id, $file, $dir)
	{
		jimport('joomla.filesystem.file');

		$user   = PFuser::GetInstance();
		$config = PFconfig::GetInstance();
		$db     = PFdatabase::GetInstance();

		$file['name'] = (array_key_exists('name', $file)) ? JFile::makeSafe($file['name']) : '';
		$desc         = JRequest::getVar('description');
		$tasks        = JRequest::getVar('tasks', array(), 'array');

		$uid  = $user->GetId();
		$now  = time();
		$name = null;

        $upath = $config->Get('upload_path', 'filemanager_pro');

		if ($file['name'] != '') {
		    $has_file = true;
			$project  = $user->GetWorkspace();
			$prefix1  = "project_".$project;
			$prefix2  = uniqid(md5($file['name']).rand(1,1000))."_";
			$filepath = JPath::clean(JPATH_ROOT.DS.$upath.DS.$prefix1);
            $size     = $file['size'] / 1024;
            $name     = $file['name'];

            $query = "SELECT name, prefix, project FROM #__pf_files"
                   . "\n WHERE id = '$id'";
                   $db->setQuery($query);
                   $tmp = $db->loadObject();

            if($tmp) {
                if($config->Get('file_vc', 'filemanager_pro') == 0) {
                   JFile::delete(JPATH_ROOT.DS.$upath.DS."project_".$tmp->project.DS.$tmp->prefix.strtolower($tmp->name));
                }
            }

            // Create the upload path if it does not exist
			if(!JFolder::exists($filepath)) {
					JFolder::create($filepath, '0777');
			}
			else {
				if(JPath::canChmod($filepath)) {
			       JPath::setPermissions($filepath, '0644', '0777');
		        }
			}

			if (!JFile::upload($file['tmp_name'], $filepath.DS.$prefix2.strtolower(JFile::makeSafe($file['name'])))) {
				$name = null;
			}

			// chmod upload folder
		    if(JPath::canChmod($filepath)) {
		        JPath::setPermissions($filepath, '0644', '0755');
		    }
		    $up = ", prefix = '$prefix2'";
		}
		else {
		    $has_file = false;
			$up = "";
		}

		if($name) {
            $v_name   = $name;
            $v_prefix = $prefix2;
			$name = " name = ".$db->quote($name).",";
		}
		else {
            $query = "SELECT name, prefix, filesize FROM #__pf_files"
                   . "\n WHERE id = '$id'";
                   $db->setQuery($query);
                   $row = $db->loadObject();

            $v_name   = $row->name;
            $v_prefix = $row->prefix;
            $size     = $row->filesize;
			$name     = "";
		}

		$query = "UPDATE #__pf_files SET $name description = ".$db->quote($desc).","
               . "\n filesize = '$size', edate = ".$db->quote($now)." "
		       . "\n $up WHERE id = ".$db->quote($id);
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
			$this->AddError($db->getErrorMsg());
			return false;
		}

		// Update version description of last rev if no file uploaded
		if(!$has_file) {
            $query = "SELECT MAX(id) FROM #__pf_file_versions WHERE file_id = '$id'";
                   $db->setQuery($query);
                   $rev_id = $db->loadResult();

            if($rev_id) {
                $query = "UPDATE #__pf_file_versions SET description = ".$db->quote($desc)
                       . "\n WHERE id = '$rev_id'";
                       $db->setQuery($query);
                       $db->query();
            }
        }

		// Update task connections
		if((int) $config->Get('attach_files', 'filemanager_pro')) {
			$this->UpdateAttachments($id, 'file', $tasks);
		}

        // Create new version
		if($config->get('file_vc', 'filemanager_pro') == '1' && $has_file) {
			 $this->CreateFileVersion($id,$v_name,$v_prefix,$desc,$uid,$size,$now);
		}

        $data = array($id);
        PFprocess::Event('update_file', $data);

		return true;
	}

	public function SaveAttachments($id, $type, &$tasks)
	{
	    $db = PFdatabase::GetInstance();

		$id     = $db->Quote($id);
		$type   = $db->Quote($type);
		$looped = array();

		foreach ($tasks AS $task)
		{
			$task = (int) $task;

			if(!$task || in_array($task, $looped)) continue;

            $query = "INSERT INTO #__pf_task_attachments VALUES(NULL,$task,$id,$type)";
			       $db->setQuery($query);
			       $db->query();

			if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
				return false;
			}

			$looped[] = $task;
		}

        $data = array($id, $type, $tasks);
        PFprocess::Event('save_attachment', $data);

        return true;
	}

	public function SaveFolderAccess($id, $dir, $groups)
	{
        $db = PFdatabase::GetInstance();

        // Find parent access
        $parent_groups = $this->LoadFolderAccess($dir);

        if(count($parent_groups)) {
            foreach($parent_groups AS $g)
            {
                if(!in_array($g, $groups)) $groups[] = $g;
            }
        }

        $looped = array();
        foreach($groups AS $g)
        {
            $g = (int) $g;

            if(in_array($g, $looped)) continue;
            $looped[] = $g;

            $query = "INSERT INTO #__pf_folder_access VALUES ("
                   . "\n NULL, '$id', '$g'"
                   . "\n )";
                   $db->setQuery($query);
                   $db->query();

            if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }
        }

        $data = array($id, $dir, $looped);
        PFprocess::Event('save_folder_access', $data);

        return true;
    }

	public function UpdateAttachments($id, $type, &$tasks)
	{
	    $db = PFdatabase::GetInstance();

		$query = "DELETE FROM #__pf_task_attachments"
               . "\n WHERE attach_id = '$id' AND attach_type = '$type'";
		       $db->setQuery($query);
			   $db->query();

        if($db->getErrorMsg()) {
			$this->AddError($db->getErrorMsg());
			return false;
		}

        $data = array($id, $type, $tasks);
        PFprocess::Event('update_attachments', $data);

		return $this->SaveAttachments($id, $type, $tasks);
	}

	public function UpdateFolderAccess($id, $dir, $new_groups, $old_groups)
	{
        $db = PFdatabase::GetInstance();

        // Get old id's
        $tmp_old_groups = $old_groups;
        $old_groups = array_values($old_groups);

        // Find parent access
        $parent_groups  = $this->LoadFolderAccess($dir);

        // Find current groups
        $current_groups = $this->LoadFolderAccess($id);

        // Find child folders
        $child_folders = array();
        $childs_num    = 0;
        $query = "SELECT folder_id FROM #__pf_folder_tree"
               . "\n WHERE parent_id = '$id'";
               $db->setQuery($query);
               $tmp_folders = $db->loadResultArray();

        if(!is_array($tmp_folders)) $tmp_folders = array();
        $childs_num = count($tmp_folders);
        $child_folders = $tmp_folders;

        while($childs_num != 0)
        {
            foreach($tmp_folders AS $f)
            {
                $query = "SELECT folder_id FROM #__pf_folder_tree"
                       . "\n WHERE parent_id = '$f'";
                       $db->setQuery($query);
                       $tmp_folders = $db->loadResultArray();

                if(!is_array($tmp_folders)) $tmp_folders = array();
                $childs_num = count($tmp_folders);

                if($childs_num) $child_folders = array_merge($child_folders, $tmp_folders);
            }
        }

        $childs_num = count($child_folders);
        $imp_child_folders = implode(',',$child_folders);

        // Update current folder
        if(count($parent_groups)) {
            foreach($parent_groups AS $g)
            {
                if(!in_array($g, $new_groups)) $new_groups[] = $g;
            }
        }

        $new_current_groups = array();
        foreach($current_groups AS $g)
        {
            if(in_array($g, $old_groups)) continue;
            $new_current_groups[] = $g;
        }
        $current_groups = $new_current_groups;

        $looped = $current_groups;
        foreach($new_groups AS $g)
        {
            if(in_array($g, $looped)) continue;
            if(in_array($g, $old_groups)) continue;
            $looped[] = $g;

            $query = "INSERT INTO #__pf_folder_access VALUES ("
                   . "\n NULL, '$id', '$g'"
                   . "\n )";
                   $db->setQuery($query);
                   $db->query();

            if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }
        }

        $old_groups = $tmp_old_groups;

        // Delete
        foreach($old_groups AS $gid => $opt)
        {
            $gid = (int) $gid;
            $opt = (int) $opt;

            if($opt > 0) continue;

            // Delete from this dir
            $query = "DELETE FROM #__pf_folder_access"
                   . "\n WHERE (folder_id = '$id'"
                   . "\n AND group_id = '$gid')";
                   $db->setQuery($query);
                   $db->query();

            if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }

            // Delete from sub-folders too
            if($opt == -2 && $childs_num != 0) {
                $query = "DELETE FROM #__pf_folder_access"
                       . "\n WHERE (folder_id IN($imp_child_folders)"
                       . "\n AND group_id = '$gid')";
                       $db->setQuery($query);
                       $db->query();

                if($db->getErrorMsg()) {
			        $this->AddError($db->getErrorMsg());
			        return false;
		        }
            }
        }

        // Add missing groups to child folders
        $current_groups = $this->loadFolderAccess($id);

        foreach($child_folders AS $f)
        {
            $child_access = $this->LoadFolderAccess($f);

            foreach($current_groups AS $ng)
            {
                if(in_array($ng, $child_access)) continue;

                $query = "INSERT INTO #__pf_folder_access VALUES("
                       . "\n NULL, '$f', '$ng'"
                       . "\n )";
                       $db->setQuery($query);
                       $db->query();

                if($db->getErrorMsg()) {
			        $this->AddError($db->getErrorMsg());
			        return false;
		        }
            }
        }


        return true;
    }

	public function DeleteFolder($dir, $folder)
	{
	    $db   = PFdatabase::GetInstance();
	    $user = PFuser::GetInstance();

		$folders = array();
		if(is_array($folder)) $folders = array_merge($folders,$folder);

		$tmp_folder  = implode(',', $folder);
		$sub_folders = array();

		if($tmp_folder != '') {
            $query = "SELECT folder_id FROM #__pf_folder_tree"
                   . "\n WHERE parent_id IN($tmp_folder)";
		           $db->setQuery($query);
		           $sub_folders = $db->loadResultArray();
        }

		if(count($sub_folders)) $folders = array_merge($folders, $sub_folders);

		while(count($sub_folders))
		{
			$tmp_sub_folders = implode(',', $sub_folders);
			$sub_folders = array();

			if($tmp_sub_folders != '') {
                $query = "SELECT folder_id FROM #__pf_folder_tree"
                       . "\n WHERE parent_id IN($tmp_sub_folders)";
		               $db->setQuery($query);
		               $sub_folders = $db->loadResultArray();

		        if(!is_array($sub_folders)) $sub_folders = array();
            }

		    if(count($sub_folders)) {
		    	$folders = array_merge($folders, $sub_folders);
		    }
		}

		$deleteable  = array();
		$move        = array();
        $looped      = array();

		$tmp_folders = implode(',', $folders);

		// Check folder access
		$query = "SELECT id,author FROM #__pf_folders WHERE id IN($tmp_folders)";
		       $db->setQuery($query);
		       $rows = $db->loadObjectList();

        if(!is_array($rows)) $rows = array();

		foreach($rows AS $row)
        {
            if(in_array($row->id, $looped)) continue;
            $looped[] = $row->id;

            if(!$user->Access('task_delete', 'filemanager_pro', $row->author)) {
                $move[] = $row->id;
            }
            else {
                $deleteable[] = $row->id;
            }
        }

        $tmp_folders = implode(', ',$deleteable);
        $folders     = $deleteable;

		if($tmp_folders == '') {
            $this->AddError('NOT_AUTHORIZED');
            return false;
        }

		// Check folder access
		foreach($folders AS $f)
		{
            if(!$this->CheckFolderAccess($f)) {
                $this->AddError("MSG_FOLDER_RESTRICTED_DEL");
                return false;
            }
        }

		$tmp_folders = implode(',', $folders);

		if($tmp_folders) {
            $query = "DELETE FROM #__pf_folders WHERE id IN($tmp_folders)";
    		       $db->setQuery($query);
    		       $db->query();

    		if($db->getErrorMsg()) {
                $this->AddError($db->getErrorMsg());
                return false;
            }

    		$query = "DELETE FROM #__pf_folder_tree WHERE folder_id IN($tmp_folders)";
    		       $db->setQuery($query);
    		       $db->query();

    		if($db->getErrorMsg()) {
                $this->AddError($db->getErrorMsg());
                return false;
            }
        }

        // Move non-deletable folders to current level
        $tmp_folders = implode(', ',$move);

        if($tmp_folders != '') {
            $query = "UPDATE #__pf_folder_tree SET parent_id = '$dir'"
                   . "\n WHERE folder_id IN($tmp_folders)";
                   $db->setQuery($query);
                   $db->query();

            if($db->getErrorMsg()) {
    			$this->AddError($db->getErrorMsg());
    			return false;
    		}
        }

		$data = array($folder);
        PFprocess::Event('delete_files', $data);

		return $folders;
	}

	public function DeleteNotes($document, $folders = array(), $dir = 0)
	{
	    $db       = PFdatabase::GetInstance();
	    $user     = PFuser::GetInstance();
		$document = implode(',', $document);
		$tmp_folders = $folders;

		// Check folder access
        if($dir) {
            $folder = $this->LoadFolder($dir);
            if(!$this->CheckFolderAccess($folder)) {
                $this->AddError('MSG_NOTE_RESTRICTED_DEL');
                return false;
            }
        }

		if($document != '') {
		    $delete = array();

		    // Check access
		    $query = "SELECT id, author FROM #__pf_notes WHERE id IN($document)";
		           $db->setQuery($query);
		           $rows = $db->loadObjectList();

            if(!is_array($rows)) $rows = array();

            foreach($rows AS $row)
            {
                if($user->Access('task_delete', 'filemanager_pro', $row->author)) {
                    $delete[] = $row->id;
                }
            }

            $document = implode(', ',$delete);
            if(!$document && count($folders) == 0) {
                $this->AddError('NOT_AUTHORIZED');
                return false;
            }

            $query = "DELETE FROM #__pf_notes WHERE id IN($document)";
		           $db->setQuery($query);
		           $db->query();

		    if($db->getErrorMsg()) {
                $this->AddError($db->getErrorMsg());
                return false;
            }

		    $query = "DELETE FROM #__pf_note_versions WHERE note_id IN($document)";
		           $db->setQuery($query);
		           $db->query();

		    if($db->getErrorMsg()) {
                $this->AddError($db->getErrorMsg());
                return false;
            }

		    $query = "DELETE FROM #__pf_note_properties WHERE note_id IN($document)";
		           $db->setQuery($query);
		           $db->query();

		    if($db->getErrorMsg()) {
                $this->AddError($db->getErrorMsg());
                return false;
            }
        }

	    if(count($folders)) {
	    	$folders = implode(',', $tmp_folders);

	    	$delete  = array();
            $move    = array();

            $query = "SELECT id, author FROM #__pf_notes WHERE dir IN($folders)";
                   $db->setQuery($query);
                   $docs = $db->loadObjectList();

            if(!is_array($docs)) $docs = array();

            if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }

		    // Check access
		    foreach($docs AS $row)
		    {
                if(!$user->Access('task_delete', 'filemanager_pro', $row->author)) {
                    $move[] = $row->id;
                }
                else {
                    $delete[] = $row->id;
                }
            }

            $delete = implode(', ',$delete);
            $move   = implode(', ', $move);

            if($delete) {
                $query = "DELETE FROM #__pf_notes WHERE id IN($delete)";
		           $db->setQuery($query);
		           $db->query();

                if($db->getErrorMsg()) {
                    $this->AddError($db->getErrorMsg());
                    return false;
                }

	    		$query = "DELETE FROM #__pf_note_versions WHERE note_id IN($delete)";
	                   $db->setQuery($query);
	                   $db->query();

                if($db->getErrorMsg()) {
                    $this->AddError($db->getErrorMsg());
                    return false;
                }

	            $query = "DELETE FROM #__pf_note_properties WHERE note_id IN($delete)";
	                   $db->setQuery($query);
	                   $db->query();

	            if($db->getErrorMsg()) {
                    $this->AddError($db->getErrorMsg());
                    return false;
                }
            }

            if($move) {
                $query = "UPDATE #__pf_notes SET dir = '$dir' WHERE id IN($move)";
    		           $db->setQuery($query);
    		           $db->query();

    		    if($db->getErrorMsg()) {
    			    $this->AddError($db->getErrorMsg());
    			    return false;
    		    }
            }

	    }

        $data = array($document, $folders);
        PFprocess::Event('delete_note', $data);

        return true;
	}

	public function DeleteFiles($file, $folders = array(), $dir = 0)
	{
		jimport('joomla.filesystem.file');

		$db     = PFdatabase::GetInstance();
		$config = PFconfig::GetInstance();
		$user   = PFuser::GetInstance();

		if(!is_array($file)) $file = array();
		$file  = implode(',', $file);
		$files = array();
		$delete = array();
		$e     = false;

		// Check folder access
        if($dir) {
            $folder = $this->LoadFolder($dir);
            if(!$this->CheckFolderAccess($folder)) {
                $this->AddError('MSG_FILE_RESTRICTED_DEL');
                return false;
            }
        }

		if($file != '') {
            $query = "SELECT id, name, project, author, prefix FROM #__pf_files WHERE id IN($file)";
		           $db->setQuery($query);
		           $files = $db->loadObjectList();

		    if(!is_array($files)) $files = array();
        }

		foreach ($files AS $f)
		{
		    if(!$user->Access('task_delete', 'filemanager_pro', $f->author)) continue;
    		$delete[] = $f->id;

			$path = PFfilemanagerHelper::FilePath($f->project,$f->name,$f->prefix);

			if(file_exists($path)) {
				JFile::delete($path);
			}
			else {
				$e = true;
				$this->AddError(str_replace('{file}', $path, PFformat::Lang('MSG_FM_FILE_NOT_EXISTS')));
			}

            // Delete file versions
            $query = "SELECT id, name, prefix FROM #__pf_file_versions WHERE file_id = '$f->id'";
                   $db->setQuery($query);
                   $versions = $db->loadObjectList();

            if (!is_array($versions)) $versions = array();

            foreach($versions AS $version)
            {
			    $path = PFfilemanagerHelper::FilePath($f->project,$version->name,$version->prefix);
                if(file_exists($path)) JFile::delete($path);
            }

            $query = "DELETE FROM #__pf_file_versions WHERE file_id = '$f->id'";
                   $db->setQuery($query);
                   $db->query();
		}

		$delete = implode(', ', $delete);

		if(!$delete && !count($folders) && $file != '') {
            $this->AddError('NOT_AUTHORIZED');
            return true;
        }

		if($delete != '') {
		    $query = "DELETE FROM #__pf_files WHERE id IN($delete)";
		           $db->setQuery($query);
		           $db->query();

		    $query = "DELETE FROM #__pf_task_attachments WHERE attach_id IN($delete)"
                   . "\n AND attach_type = 'file'";
                   $db->setQuery($query);
                   $db->query();
		}

	    if(count($folders)) {
	    	$folders = implode(',', $folders);
	    	$delete  = array();
	    	$move    = array();

	    	$query = "SELECT id, name, project, prefix, author FROM #__pf_files WHERE dir IN($folders)";
		           $db->setQuery($query);
		           $files = $db->loadObjectList();

		    if(!is_array($files)) $files = array();

		    foreach ($files AS $f)
		    {
		        if(!$user->Access('task_delete', 'filemanager_pro', $f->author)) {
                    $move[] = $f->id;
                    continue;
                }
    		    $delete[] = $f->id;

			    $path = $path = PFfilemanagerHelper::FilePath($f->project,$f->name,$f->prefix);

			    if(file_exists($path)) {
				    JFile::delete($path);
			    }
			    else {
			    	$e = true;
				    $this->AddError(str_replace('{file}', $path, PFformat::Lang('MSG_FM_FILE_NOT_EXISTS')));
			    }

                // Delete file versions
                $query = "SELECT name, prefix FROM #__pf_file_versions WHERE file_id = '$f->id'";
                       $db->setQuery($query);
                       $versions = $db->loadObjectList();

                if(!is_array($versions)) $versions = array();

                foreach($versions AS $version)
                {
			        $path = $path = PFfilemanagerHelper::FilePath($f->project,$version->name,$version->prefix);
                    if(file_exists($path)) JFile::delete($path);
                }

                $query = "DELETE FROM #__pf_file_versions WHERE file_id = '$f->id'";
                       $db->setQuery($query);
                       $db->query();
		    }

		    $delete = implode(', ', $delete);
		    $move   = implode(', ', $move);

		    if($delete) {
                $query = "DELETE FROM #__pf_files WHERE id IN($delete)";
		               $db->setQuery($query);
		               $db->query();
            }

            if($move) {
                $query = "UPDATE #__pf_files SET dir = '$dir' WHERE id IN($move)";
                       $db->setQuery($query);
                       $db->query();
            }
	    }

        $data = array($file, $folders);
        PFprocess::Event('delete_files', $data);

	    if($e) return false;
	    return true;
	}

	public function Move($dir = 0, $folders = array(), $files = array(), $notes = array())
	{
	    $db = PFdatabase::GetInstance();

		foreach ($folders AS $id)
		{
			$id = (int) $id;

			$query = "UPDATE #__pf_folder_tree SET parent_id = '$dir'"
                   . "\n WHERE folder_id = '$id'";
			       $db->setQuery($query);
			       $db->query();

			if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }
		}

		foreach ($files AS $id)
		{
			$id = (int) $id;

			$query = "UPDATE #__pf_files SET dir = '$dir'"
                   . "\n WHERE id = '$id'";
			       $db->setQuery($query);
			       $db->query();

			if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }
		}

		foreach ($notes AS $id)
		{
			$id = (int) $id;

			$query = "UPDATE #__pf_notes SET dir = '$dir'"
                   . "\n WHERE id = '$id'";
			       $db->setQuery($query);
			       $db->query();

			if($db->getErrorMsg()) {
			    $this->AddError($db->getErrorMsg());
			    return false;
		    }
		}

		return true;
	}

	public function CreateNoteVersion($id, $title, $desc, $content, $author, $time = 0)
	{
	    $db = PFdatabase::GetInstance();

	    $title   = $db->Quote($title);
	    $desc    = $db->Quote($desc);
	    $content = $db->Quote($content);
		if(!$time) $time = time();

		$query = "INSERT INTO #__pf_note_versions VALUES("
               . "\n NULL, $id, $title, $desc, $content, $author, $time"
               . "\n )";
		       $db->setQuery($query);
		       $db->query();

		$nid = $db->insertid();
		if(!$nid) return NULL;

        // Update modify-date of the original file
        $query = "UPDATE #__pf_notes SET edate = $time WHERE id = $id";
               $db->setQuery($query);
               $db->query();

		return $nid;
	}

    public function CreateFileVersion($id, $name, $prefix, $desc, $author, $size = 0, $time = 0)
	{
	    $db = PFdatabase::GetInstance();

	    $name   = $db->Quote($name);
	    $prefix = $db->Quote($prefix);
	    $desc   = $db->Quote($desc);
		if(!$time) $time = time();

		$query = "INSERT INTO #__pf_file_versions VALUES("
               . "\n NULL, $id, $name, $prefix, $desc, $author, $size, $time"
               . "\n )";
		       $db->setQuery($query);
		       $db->query();

        $nid = $db->insertid();
        if(!$nid) return NULL;

        // Update modify-date of the original file
        $query = "UPDATE #__pf_files SET edate = $time WHERE id = $id";
               $db->setQuery($query);
               $db->query();

		return $nid;
	}

    public function CheckoutNote($id, $uid)
	{
        $db = PFdatabase::GetInstance();

		$query = "UPDATE #__pf_note_properties"
               . "\n SET checked_out = '1', checked_out_user = '$uid'"
               . "\n WHERE note_id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

        return true;
	}

    public function CheckoutFile($id, $uid)
	{
	    $db = PFdatabase::GetInstance();

		$query = "UPDATE #__pf_file_properties"
               . "\n SET checked_out = '1', checked_out_user = '$uid'"
               . "\n WHERE file_id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

        return true;
	}

	public function CheckinNote($id)
	{
	    $db = PFdatabase::GetInstance();

		$query = "UPDATE #__pf_note_properties SET checked_out = '0', checked_out_user = '0'"
               . "\n WHERE note_id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

        return true;
	}

    public function CheckinFile($id)
    {
        $db = PFdatabase::GetInstance();

        $query = "UPDATE #__pf_file_properties SET checked_out = '0', checked_out_user = '0'"
               . "\n WHERE file_id = '$id'";
		       $db->setQuery($query);
		       $db->query();

		if($db->getErrorMsg()) {
            $this->AddError($db->getErrorMsg());
            return false;
        }

        return true;
    }

    public function GetRealNoteId($id, $note_id)
    {
        $db = PFdatabase::GetInstance();

        $query = "SELECT COUNT(id) FROM #__pf_note_versions WHERE id <= '$id' AND note_id = '$note_id'";
               $db->setQuery($query);
               $real_id = $db->loadResult();

        return $real_id;
    }

    public function CheckFolderAccess($object)
    {
        static $cache = array();

        if(is_object($object)) {
            $id = $object->id;
            if(array_key_exists($id, $cache)) $cache[$id];

            $user   = PFuser::GetInstance();
            $uid    = $user->GetId();
            $flag   = $user->GetFlag();

            if($flag == 'system_administrator' || $flag == 'project_administrator' || $uid == $object->author) {
                $cache[$id] = true;
                return true;
            }

            $groups = $user->Permission('groups');
            $folder_groups = $this->LoadFolderAccess($id);

            if(count($folder_groups) == 0) {
                $cache[$id] = true;
                return true;
            }

            foreach($folder_groups AS $g)
            {
                if(in_array($g, $groups)) {
                    $cache[$id] = true;
                    return true;
                }
            }

            $cache[$id] = false;
            return false;
        }
        else {
            if(array_key_exists($object, $cache)) return $cache[$object];

            $user = PFuser::GetInstance();
            $flag = $user->GetFlag();

            if($flag == 'system_administrator' || $flag == 'project_administrator') {
                $cache[$object] = true;
                return true;
            }

            $object = $this->LoadFolder($object);

            return $this->CheckFolderAccess($object);
        }
    }
}
?>