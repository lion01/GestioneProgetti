<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


class PFfilemanagerTree
{
    public function Render($dir = 0, $lvl = 0, $workspace = 0, $root = true)
    {
        static $open   = NULL;
        static $db     = NULL;
        static $class  = NULL;
        static $user   = NULL;
        if(!$workspace) return "";

        if(is_null($open)) {
            $current_dir = (int) JRequest::getVar('dir');
            $open = PFfilemanagerTree::GetOpenDirs($current_dir);
            $open[] = $current_dir;
        }

        if(is_null($db))    $db = PFdatabase::GetInstance();
        if(is_null($user))  $user = PFuser::GetInstance();
        if(is_null($class)) $class = new PFfilemanagerClass();

        $u_flag = $user->GetFlag();

        $query = "SELECT f.id, f.title, t.parent_id, a.folder_id AS restricted FROM #__pf_folders AS f"
               . "\n RIGHT JOIN #__pf_folder_tree AS t ON t.folder_id = f.id"
               . "\n LEFT JOIN #__pf_folder_access AS a ON a.folder_id = f.id"
               . "\n WHERE t.parent_id = '$dir'"
               . "\n AND f.project = '$workspace'"
               . "\n GROUP BY f.id"
               . "\n ORDER BY f.title ASC";
               $db->setQuery($query);
               $folders = $db->loadObjectList();

        if(!is_array($folders)) $folders = array();

        if($root) {
            $html = '<ul class="pf_tree"><li class="active_tree fmpro_root">
            <a href="'.PFformat::Link("section=filemanager_pro&dir=0").'" class="pf_fm_folder"><span>'.PFformat::Lang('FM_ROOT').'</span></a>
            </li><li>';
        }
        else {
            $html = '';
        }

        if($dir == 0) {
            $html .= '<ul class="pf_tree">';
        }
        else {
            if(in_array($dir, $open)) {
                $html .= '<ul id="dir_'.$lvl.'" class="pf_tree">';
                $lvl++;
            }
            else {
                $html .= '<ul id="dir_'.$lvl.'" class="pf_tree" style="display:none">';
                $lvl++;
            }
        }

        $i = 0;
        foreach($folders AS $folder)
        {
            JFilterOutput::objectHTMLSafe($folder);

            // Check folder access
            if($folder->restricted) {
                if(!$class->CheckFolderAccess($folder->id)) {
                    $i++;
                    continue;
                }
            }

            $link = PFformat::Link("section=filemanager_pro&dir=".$folder->id);

            $query = "SELECT COUNT(id) FROM #__pf_folder_tree"
                   . "\n WHERE parent_id = '$folder->id'"
                   . "\n AND folder_id != '$folder->id'";
                   $db->setQuery($query);
                   $has_children = $db->loadResult();

            if($has_children >= 1) {
                $lvl++;
                $symbol  = (in_array($folder->id, $open)) ? '-' : '+';
                $active  = (in_array($folder->id, $open)) ? 'active_tree' : 'inactive_tree';
                $onclick = 'toggle_tree('.$lvl.', '.$i.')';
                $a_id    = 'dirtoggle_'.$lvl.'_'.$i;

                $html .= '<li class="'.$active.'">
                    <a onclick="'.$onclick.'" id="'.$a_id.'" class="tree_handle"><span>'.$symbol.'</span></a>
                    <a href="'.$link.'" class="pf_fm_folder"><span>'.$folder->title.'</span></a>
                </li>';

                $html .= '<li>'.PFfilemanagerTree::Render($folder->id, $lvl, $workspace, false).'</li>';
            }
            else {
                $active  = (in_array($folder->id, $open)) ? 'active_tree' : 'inactive_tree';
                $html .= '<li class="'.$active.'">
                    <span class="tree_handle2">&bull;</span>
                    <a href="'.$link.'" class="pf_fm_folder"><span>'.$folder->title.'</span></a>
                </li>';
            }
            $i++;
        }

        if($root) {
            $html .= '</ul></li></ul>';
        }
        else {
            $html .= '</ul>';
        }


        return $html;
    }

    public function GetOpenDirs($dir)
    {
        static $db = NULL;

        if($dir == 0) return array();
        if(is_null($db)) $db = PFdatabase::GetInstance();

        $dir_array = array();

        $query = "SELECT parent_id FROM #__pf_folder_tree WHERE folder_id = '$dir'";
               $db->setQuery($query);
               $tmp_dir_array = $db->loadResultArray();

        if(is_array($tmp_dir_array)) {
           foreach($tmp_dir_array AS $arr)
           {
               $dir_array[] = $arr;
               $dir_array = array_merge($dir_array, PFfilemanagerTree::GetOpenDirs($arr));
           }
        }
        else {
            return array();
        }

        return $dir_array;
    }
}


class PFfilemanagerHelper
{
    public function RenderAddressBar($dir = 0)
    {
        $core   = PFcore::GetInstance();
        $db     = PFdatabase::GetInstance();
        $config = PFconfig::GetInstance();

        // Check if enabled
        $enabled = (int) $config->Get('use_addressbar', 'filemanager_pro');
        if(!$enabled) return "";

        $task = $core->GetTask();

		$query = "SELECT parent_id FROM #__pf_folder_tree"
               . "\n WHERE folder_id = ".$db->quote($dir);
		       $db->setQuery($query);
		       $parent = (int) $db->loadResult();

		$dir_array = array();
		if($parent) $dir_array[] = $parent;

		$link = ($task == 'list_move') ? "javascript:list_move(0)" : PFformat::Link('section=filemanager_pro&dir=0');

        $html = '<a href="'.$link.'" class="pf_fm_folder"><span>'.PFformat::Lang('FM_ROOT').'</span></a><span>'.DS.'</span>';

		while ($parent >= 1) {
		 	$query = "SELECT parent_id FROM #__pf_folder_tree"
                   . "\n WHERE folder_id = ".$db->quote($parent);
		           $db->setQuery($query);
		           $parent = (int) $db->loadResult();

		    if($parent) $dir_array[] = $parent;
		}

		foreach ($dir_array AS $i => $id)
		{
			$query = "SELECT title FROM #__pf_folders"
                   . "\n WHERE id = ".$db->quote($id);
			       $db->setQuery($query);
			       $title = $db->loadResult();

            $link = ($task == 'list_move') ? "javascript:list_move($id)" : PFformat::Link('section=filemanager_pro&dir='.$id);

			$html .= '<a href="'.$link.'" class="pf_fm_folder"><span>'.$title.'</span></a><span>'.DS.'</span>';
		}

		if($dir) {
			$query = "SELECT title FROM #__pf_folders"
                   . "\n WHERE id = ".$db->quote($dir);
			       $db->setQuery($query);
			       $title = $db->loadResult();

			$html .= '<a href="'.PFformat::Link('section=filemanager_pro&dir='.$dir).'" class="pf_fm_folder">
                          <span>'.$title.'</span>
                      </a>
                      <span>'.DS.'</span>';
		}

		return $html;
    }

    public function RenderSelectNoteVersion($name, $id, $v = 0, $onchange = 'onchange="document.adminForm.submit();"')
    {
        $db = PFdatabase::GetInstance();

        $query = "SELECT id, title FROM #__pf_note_versions"
               . "\n WHERE note_id = '$id'"
               . "\n ORDER BY id DESC";
               $db->setQuery($query);
               $rows = $db->loadObjectList();

        $h = "<select name='$name' $onchange>";
        $count = count($rows);

        foreach($rows AS $i => $row)
        {
            JFilterOutput::objectHTMLSafe($row);

            $s = "";
            if($row->id == $v) $s = 'selected="selected"';

            $h .= "<option value='$row->id' $s>#".($count-$i);
            $h .= " - ".$row->title."</option>";
        }

        $h .= "</select>";

        return $h;
    }

    public function FilePath($project, $name, $prefix)
    {
        static $c_path = NULL;

        if(is_null($c_path)) {
            $config = PFconfig::GetInstance();
            $c_path = $config->Get('upload_path', 'filemanager_pro');
            $c_path = str_replace('/', DS, $c_path);
            $c_path = str_replace('\\', DS, $c_path);

            if(substr($c_path,0,1) == DS) $c_path = substr($c_path,1);
            if(substr($c_path,-1) == DS)  $c_path = substr($c_path,0, (strlen($c_path - 1)));
        }

        $prefix1 = "project_".$project;
		$prefix2 = $prefix;
		$path = JPATH_ROOT.DS.$c_path.DS.$prefix1.DS.$prefix2.strtolower($name);

		return $path;
    }

    public function GetUploadPath()
    {
        static $c_path = NULL;

        if(is_null($c_path)) {
            $config = PFconfig::GetInstance();
            $c_path = $config->Get('upload_path', 'filemanager_pro');
            $c_path = str_replace('/', DS, $c_path);
            $c_path = str_replace('\\', DS, $c_path);

            if(substr($c_path,0,1) == DS) $c_path = substr($c_path,1);
            if(substr($c_path,-1) == DS)  $c_path = substr($c_path,0, (strlen($c_path - 1)));
        }

        $path = JPATH_ROOT.DS.$c_path;

        return $path;
    }

    public function SelectNewAccessGroup($name, $preselect = 0, $disabled = false)
    {
        static $project_groups = NULL;

        $user = PFuser::GetInstance();
        $workspace = $user->GetWorkspace();

        if(is_null($project_groups)) {
            $db = PFdatabase::GetInstance();

            if($workspace) {
                $query = "SELECT id, title FROM #__pf_groups"
                       . "\n WHERE project = '$workspace'"
                       . "\n ORDER BY title ASC";
                       $db->setQuery($query);
                       $project_groups = $db->loadObjectList();

                if(!is_array($project_groups)) $project_groups = array();
            }
            else {
                $project_groups = array();
            }
            unset($db);
        }

        $d = ($disabled == true) ? ' disabled="disabled"' : '';

        $html = '<select name="'.$name.'"'.$d.'>
        <option value="0">'.PFformat::Lang('SELECT_GROUP').'</option>';

        $found = false;
        foreach($project_groups AS $g)
        {
            $v = (int) $g->id;
            $l = htmlspecialchars($g->title);
            $s = ($preselect == $v) ? ' selected="selected"' : '';
            if($preselect == $v) $found = true;
            $html .= '<option value="'.$v.'"'.$s.'>'.$l.'</option>';
        }

        if($preselect != 0 && $found == false) return PFfilemanagerHelper::SelectEditAccessGroup('oldgroups['.$preselect.']', $preselect);

        $html .= '</select>';

        return $html;
    }

    public function SelectEditAccessGroup($name, $preselect = 0)
    {
        $db = PFdatabase::GetInstance();

        $query = "SELECT id, title FROM #__pf_groups"
               . "\n WHERE id = '$preselect'";
               $db->setQuery($query);
               $row = $db->loadObject();

        if(!is_object($row)) {
            $html = '<select name="'.$name.'">
            <option value="-2" selected="selected">'.PFformat::Lang('GROUP_DEL_OPT_3').'</option>';
        }
        else {
            $html = '<select name="'.$name.'">
            <option value="'.$preselect.'" selected="selected">'.htmlspecialchars($row->title).'</option>
            <optgroup label="'.PFformat::Lang('GROUP_DEL_OPTS').'">
                <option value="-1">'.PFformat::Lang('GROUP_DEL_OPT_1').'</option>
                <option value="-2">'.PFformat::Lang('GROUP_DEL_OPT_2').'</option>
            </optgroup>';

        }

        $html .= '</select>';
        return $html;
    }

    public function FileNameExists($file, $dir = 0)
    {
        $db  = PFdatabase::GetInstance();
        $q_f = $db->quote(strtolower($file));

        $query = "SELECT id FROM #__pf_files"
               . "\n WHERE LOWER(name) = $q_f AND dir = '$dir'"
               . "\n LIMIT 1";
               $db->setQuery($query);
               $count = (int) $db->loadResult();

        return $count;
    }
}

class AjaxUpload
{
    private $valid_extensions;
    private $size_limit;
    private $file_field;
    private $upload_method;

    public function __construct($file_field, $valid_extensions = array(), $size_limit = 0)
    {
        // Include file layer
        jimport('joomla.filesystem.file');

        // Set upload restrictions
        $this->valid_extensions = array_map("strtolower", $valid_extensions);
        $this->size_limit       = $size_limit;

        // Check server settings
        $this->CheckServerSettings();

        // Detect upload method
        $this->file_field = $file_field;

        if(JRequest::getVar($file_field, NULL, 'get')) {
            $this->upload_method = 'xhr';
        }
        elseif(JRequest::getVar($file_field, NULL, 'files')) {
            $this->upload_method = 'form';
        }
        else {
            $this->upload_method = NULL;
        }
    }

    private function CheckServerSettings()
    {
        if(!$this->size_limit) return true;

        $post_size   = $this->ToBytes(ini_get('post_max_size'));
        $upload_size = $this->ToBytes(ini_get('upload_max_filesize'));

        if($post_size < $this->size_limit || $upload_size < $this->size_limit){
            $size = max(1, $this->size_limit / 1024 / 1024) . 'M';
            $e    = PFformat::Lang('MSG_AJAX_UPLOAD_E7');
            $e    = str_replace('{size}', $size, $e);

            die("{'error':'$e'}");
        }
    }

    private function ToBytes($str)
    {
        $val  = trim($str);
        $last = strtolower($str[strlen($str)-1]);

        switch($last)
        {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }

        return $val;
    }

    private function GetSize()
    {
        if($this->upload_method == 'form') {
            return $_FILES[$this->file_field]['size'];
        }

        if($this->upload_method == 'xhr') {
            if (isset($_SERVER["CONTENT_LENGTH"])){
                return (int) $_SERVER["CONTENT_LENGTH"];
            }
            else {
                throw new Exception('Getting content length is not supported.');
            }
        }

        return 0;
    }

    private function GetName()
    {
        if($this->upload_method == 'form') return JFile::makeSafe($_FILES[$this->file_field]['name']);
        if($this->upload_method == 'xhr')  return JFile::makeSafe($_GET[$this->file_field]);

        return NULL;
    }

    private function Save($path)
    {
        if($this->upload_method == 'form') {
            if(!JFile::copy($_FILES[$this->file_field]['tmp_name'], $path)) return false;
            return true;
        }

        if($this->upload_method == 'xhr') {
            $input    = fopen("php://input", "r");
            $temp     = tmpfile();
            $realSize = stream_copy_to_stream($input, $temp);
            fclose($input);

            if($realSize != $this->getSize()) return false;

            $target = fopen($path, "w");

            fseek($temp, 0, SEEK_SET);
            stream_copy_to_stream($temp, $target);
            fclose($target);

            return true;
        }

        return false;
    }

    public function HandleFile($upload_dir, $project = 0)
    {
        $upload_root = $upload_dir;
        if($project) $upload_dir = $upload_dir.DS.'project_'.$project;

        // Make sure upload dir exists
    	if(!is_dir($upload_dir)) {
            JFolder::create($upload_dir);
            JPath::setPermissions($upload_dir, '0644', '0755');
        }
        else {
            // Chmod upload root folder
		    JPath::setPermissions($upload_root, '0644', '0755');
        }

        // Make sure upload dir index.html exists
        $index   = $upload_dir.DS.'index.html';
        $content = '<html><body bgcolor="#FFFFFF"></body></html>';
        if(!file_exists($index)) JFile::write($index, $content);

        // Make sure upload root dir index.html exists
        $index = $upload_root.DS.'index.html';
        if(!file_exists($index)) JFile::write($index, $content);

		// Check file
        if(!is_writable($upload_dir)) return array('error' => PFformat::Lang('MSG_AJAX_UPLOAD_E1'));
        if (!$this->GetName())        return array('error' => PFformat::Lang('MSG_AJAX_UPLOAD_E2'));

        $size = $this->GetSize();

        if($size == 0) return array('error' => PFformat::Lang('MSG_AJAX_UPLOAD_E3'));
        if($size > $this->size_limit && $this->size_limit > 0) return array('error' => PFformat::Lang('MSG_AJAX_UPLOAD_E4'));

        $pathinfo = pathinfo($this->GetName());
        $filename = $pathinfo['filename'];
        $ext      = $pathinfo['extension'];

        // Check file type
        if(!empty($this->valid_extensions)) {
            if(!in_array(strtolower($ext), $this->valid_extensions)) {
                return array('error'=> PFformat::Lang('MSG_DESIGN_E_FILE_TYPE'));
            }
        }

        $name     = JFile::makeSafe($filename.'.'.$ext);
        $prefix   = md5(time().$name).'_';
        $filename = $prefix.strtolower($filename.'.'.$ext);

        // Upload the file
        $success = $this->Save($upload_dir.DS.$filename);

		$file = array();
		$file['name']   = $name;
		$file['prefix'] = $prefix;
		$file['size']   = round($size/1024);

        if($success) return array('success'=>true,'file'=>$file);

        return array('error'=> PFformat::Lang('MSG_AJAX_UPLOAD_E5') . PFformat::Lang('MSG_AJAX_UPLOAD_E6'));
    }
}
?>