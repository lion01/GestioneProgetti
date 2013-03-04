<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


class PFfileController extends PFobject
{
    public function __construct()
    {
        parent::__construct();
    }

	public function DisplayList()
	{
		$class = new PFfilemanagerClass();
		$form  = new PFform();

		$user   = PFuser::GetInstance();
		$config = PFconfig::GetInstance();
		$load   = PFload::GetInstance();

		// Get config settings
		$note_vc    = $config->Get('note_vc', 'filemanager_pro');
		$file_vc    = $config->Get('file_vc', 'filemanager_pro');
		$use_tree   = $config->Get('use_tree', 'filemanager_pro');
		$tree_width = $config->Get('tree_width', 'filemanager_pro');
		$desc_tt    = $config->Get('desc_tt', 'filemanager_pro');
		$quick_u    = $config->Get('quick_upload', 'filemanager_pro');
        $use_checkin = (int) $config->Get('use_checkin', 'filemanager_pro');
		$prev_size  = (int) $config->Get('prev_size', 'filemanager_pro');
		$prev_ext   = $config->Get('prev_extensions', 'filemanager_pro');
		$prev_ext   = explode(',', $prev_ext);

		// Upload URL for preview
		$upload_url = str_replace(DS, '/', $config->Get('upload_path', 'filemanager_pro'));
		$upload_url = (substr($upload_url,0,1) == '/') ? substr($upload_url,1) : $upload_url;
		$upload_url = (substr($upload_url,-1,0) != '/') ? $upload_url.'/' : $upload_url;

		unset($config);

		// Check access permissions
		$can_move   = $user->Access('list_move', 'filemanager_pro');
		$can_delete = $user->Access('task_delete', 'filemanager_pro');

		// Capture user input
        $ob = JRequest::getVar('ob', $user->GetProfile("filelist_ob", 'id'));
		$od = JRequest::getVar('od', $user->GetProfile("filelist_od", 'ASC'));
		$keyword = JRequest::getVar('keyword');

		$dir     = (int) JRequest::getVar('dir');
		$ci_note = (int) JRequest::getVar('checkin_note');
		$ci_file = (int) JRequest::getVar('checkin_file');

        // Save order settings
		$user->SetProfile("filelist_ob", $ob);
		$user->SetProfile("filelist_od", $od);

        if($dir != 0) {
            $row = $class->LoadFolder($dir);

            // Check if record exists
            if(!is_object($row)) {
                $this->SetRedirect('section=filemanager_pro&dir=0', 'MSG_ITEM_NOT_FOUND');
                return false;
            }

            // Check user access to directory
            if(!$class->CheckFolderAccess($row) && $dir != 0) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

        // Get user workspace
		$workspace = (int) $user->GetWorkspace();

	    // Check-In items?
		if($ci_note) $class->CheckinNote($ci_note);
		if($ci_file) $class->CheckInFile($ci_file);

		// Load data from db
		$folders = $class->LoadFolderList($dir, $ob, $od, $keyword, $workspace);
		$notes   = $class->LoadNoteList($dir, $ob, $od, $keyword, $workspace);
		$files   = $class->LoadFileList($dir, $ob, $od, $keyword, $workspace);
		$total   = count($folders) + count($notes) + count($files);

        $ws_title = PFformat::WorkspaceTitle();
		$nlv = false;
        $flc = false;

        // Load parent folder id
        $parent_folder = 0;
        if($dir) $parent_folder = $class->LoadParentFolderId($dir);

		if(!$tree_width) $tree_width = 150;
		if($note_vc) $nlv = $user->Access('list_note_versions', 'filemanager_pro');
        if($file_vc) $flv = $user->Access('list_file_versions', 'filemanager_pro');

		$table = new PFtable(
            array('TITLE', 'DESC', 'DATE', 'AUTHOR', 'ID'),
		    array('title', 'description', 'cdate', 'author', 'id'),
		    $ob,
		    $od
        );

		// Load Assets
		JHTML::_('behavior.modal');
		JHTML::_('behavior.mootools');

		$load->SectionCSS('filemanager_pro.css', 'filemanager_pro');

		$load->SectionJS('filemanager_pro.js', 'filemanager_pro');

        // Ajax uploader
        if(!defined('FMPRO_UPLOADER') && !defined('PF_DEMO_MODE') && $quick_u == 1) {
            define('FMPRO_UPLOADER', 1);

            $load->SectionCSS('fileuploader.css', 'filemanager_pro');
            $load->SectionJS('fileuploader.js', 'filemanager_pro');

            // New row template
            $ftmpl = '<li><tr class="pf_row0"><td class="pf_number_cell" valign="top"></td>';
            if($can_move || $can_delete) $ftmpl .= '<td align="center" class="pf_check_cell" valign="top"><span class="qq-upload-spinner"></span></td>';
            $ftmpl .= '<td valign="top"><span class="qq-upload-file"></span><span class="qq-upload-size"></span>';
            $ftmpl .= '<a class="qq-upload-cancel" href="#">'.PFformat::Lang('CANCEL').'</a>';
            $ftmpl .= '<span class="qq-upload-failed-text">'.PFformat::Lang('FAILED').'</span>';
            if($file_vc) $ftmpl .= '<small class="vc_version">'.PFformat::Lang('PFL_VERSION').': 1</small>';
            $ftmpl .= '</td><td class="pf_actions_cell" valign="top"></td>';
            if(!$desc_tt) $ftmpl .= '<td valign="top"></td>';
            $ftmpl .= '<td valign="top">'.PFformat::ToDate(time()).'</td><td valign="top">'.$user->GetName().'</td>';
            $ftmpl .= '<td class="idcol pf_id_cell" valign="top">-</td></tr></li>';

            // Init uploader
            $doc = JFactory::GetDocument();
            $doc->addScriptDeclaration("window.addEvent('domready', function createUploader(){
                var uploader = new qq.FileUploader({
                    element: document.getElementById('file-uploader'),
                    action: 'index.php',
                    params: {
                        option: 'com_databeis',
                        workspace: '".$user->GetWorkspace()."',
                        section: 'filemanager_pro',
                        task: 'task_save_file',
                        render: 'section_ajax',
                        dir: '".$dir."'
                    },
                    debug: true
                });

            });");
        }

		$form->SetBind(true, 'REQUEST');
		require_once($this->GetHelper('filemanager_pro'));
		require_once($this->GetOutput('list_directory.php'));
	}

	public function DisplayMove($dir = 0, $mfolders = array(), $mnotes = array(), $mfiles = array())
	{
	    $user = PFuser::GetInstance();
	    $load = PFload::GetInstance();
	    $db   = PFdatabase::GetInstance();

		$class = new PFfilemanagerClass();
		$form  = new PFform();

        // Validate folder access
        if(count($mfolders)) {
            $ifolders = implode(', ', $mfolders);
            $tfolders = array();

            $query = "SELECT id, author FROM #__pf_folders WHERE id IN($ifolders)";
                   $db->setQuery($query);
                   $rows = $db->loadObjectList();

            if(!is_array($rows)) $rows = array();

            foreach($rows AS $row)
            {
                if(!$user->Access('list_move', 'filemanager_pro', $row->author)) continue;
                $tfolders[] = $row->id;
                unset($row);
            }
            unset($rows);
            $mfolders = $tfolders;
        }

        // Validate note access
        if(count($mnotes)) {
            $inotes = implode(', ', $mnotes);
            $tnotes = array();

            $query = "SELECT id, author FROM #__pf_notes WHERE id IN($inotes)";
                   $db->setQuery($query);
                   $rows = $db->loadObjectList();

            if(!is_array($rows)) $rows = array();

            foreach($rows AS $row)
            {
                if(!$user->Access('list_move', 'filemanager_pro', $row->author)) continue;
                $tnotes[] = $row->id;
                unset($row);
            }
            unset($rows);
            $mnotes = $tnotes;
        }

        // Validate file access
        if(count($mfiles)) {
            $ifiles = implode(', ', $mfiles);
            $tfiles = array();

            $query = "SELECT id, author FROM #__pf_files WHERE id IN($ifiles)";
                   $db->setQuery($query);
                   $rows = $db->loadObjectList();

            if(!is_array($rows)) $rows = array();

            foreach($rows AS $row)
            {
                if(!$user->Access('list_move', 'filemanager_pro', $row->author)) continue;
                $tfiles[] = $row->id;
                unset($row);
            }
            unset($rows);
            $mfiles = $tfiles;
        }

        // Check if any data left
        if(!count($mfolders) && !count($mnotes) && !count($mfiles)) {
            $this->SetRedirect("section=filemanager_pro&dir=$dir", 'NOT_AUTHORIZED');
            return false;
        }

		$ob        = JRequest::getVar('ob', 'id');
		$od        = JRequest::getVar('od', 'ASC');
		$workspace = $user->GetWorkspace();

		$folders   = $class->LoadFolderList($dir, $ob, $od, '', $workspace);
		$total     = count($folders);
		$ws_title  = PFformat::WorkspaceTitle();

		$form->SetBind(true, 'REQUEST');

		$table = new PFtable(array('TITLE', 'DESC', 'DATE', 'AUTHOR', 'ID'),
		                     array('title', 'description', 'cdate', 'author', 'id'),
		                     'id',
		                     'ASC');

        $load->SectionCSS('filemanager_pro.css', 'filemanager_pro');
		$load->SectionJS('filemanager_pro.js', 'filemanager_pro');

		require_once($this->GetHelper('filemanager_pro'));
		require_once($this->GetOutput('list_move.php'));
	}

	public function DisplayNewFolder($dir = 0)
	{
	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();
	    $class  = new PFfilemanagerClass();
		$form   = new PFform();

		$ws_title = PFformat::WorkspaceTitle();
		$attach   = (int) $config->Get('attach_folders', 'filemanager_pro');
		$restrict = $user->Access('restrict_folder', 'filemanager_pro');

		$parent_groups = $class->LoadFolderAccess($dir);

		$form->SetBind(true, 'REQUEST');

		require_once($this->GetHelper('filemanager_pro'));
		require_once($this->GetOutput('form_new_folder.php'));
	}

	public function DisplayNewFile($dir = 0)
	{
	    if(defined('PF_DEMO_MODE')) return false;

	    $config = PFconfig::GetInstance();
		$form   = new PFform('adminForm', 'index.php', 'post', 'enctype="multipart/form-data"');

		$ws_title = PFformat::WorkspaceTitle();
		$attach   = (int) $config->Get('attach_files', 'filemanager_pro');

		$form->SetBind(true, 'REQUEST');

		require_once($this->GetOutput('form_new_file.php'));
	}

	public function DisplayNewNote($dir = 0)
	{
	    $editor   = JFactory::getEditor();
	    $config   = PFconfig::GetInstance();

		$form     = new PFform();
		$ws_title = PFformat::WorkspaceTitle();

		$attach     = (int) $config->Get('attach_notes', 'filemanager_pro');
		$use_editor = (int) $config->Get('use_editor', 'filemanager_pro');

		$form->SetBind(true, 'REQUEST');

		require_once($this->GetOutput('form_new_note.php'));
	}

	public function DisplayEditFolder($id, $dir)
	{
	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();

		$class = new PFfilemanagerClass();
		$form  = new PFform();

		$row = $class->LoadFolder($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_folder', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check user access to directory
        if(!$class->CheckFolderAccess($row)) {
            $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
            return false;
        }

        $restrict = $user->Access('restrict_folder', 'filemanager_pro', $row->author);
		$parent_groups = $class->LoadFolderAccess($dir);

        $ws_title = PFformat::WorkspaceTitle();
        $attach   = (int) $config->Get('attach_folders', 'filemanager_pro');

		$form->SetBind(true, $row);

		require_once($this->GetHelper('filemanager_pro'));
		require_once($this->GetOutput('form_edit_folder.php'));
	}

	public function DisplayEditFile($id, $dir)
	{
	    if(defined('PF_DEMO_MODE')) return false;

	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();

		$class = new PFfilemanagerClass();
		$form  = new PFform('adminForm', 'index.php', 'post', 'enctype="multipart/form-data"');
		$row   = $class->LoadFile($id);

        // Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_file', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check if checked-out
        $use_checkin = (int) $config->get('use_checkin', 'filemanager_pro');
        if($row->checked_out == '1' && ($row->checked_out_user != $user->GetId()) && $use_checkin) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_IS_CHECKED_OUT');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

        // Check-out the file
		$class->CheckoutFile($id, $user->GetId());

		$ws_title = PFformat::WorkspaceTitle();
		$attach   = (int) $config->Get('attach_files', 'filemanager_pro');

		$form->SetBind(true, $row);

		require_once($this->GetOutput('form_edit_file.php'));
	}

	public function DisplayEditNote($id, $dir)
	{
	    $editor = JFactory::getEditor();
	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();
		$class  = new PFfilemanagerClass();
		$form   = new PFform();

		$row = $class->LoadNote($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_note', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check if checked-out
        $use_checkin = (int) $config->get('use_checkin', 'filemanager_pro');
        if($row->checked_out == '1' && ($row->checked_out_user != $user->GetId()) && $use_checkin) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_IS_CHECKED_OUT');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		$ws_title = PFformat::WorkspaceTitle();

		$attach     = (int) $config->Get('attach_notes', 'filemanager_pro');
		$use_editor = (int) $config->Get('use_editor', 'filemanager_pro');

		$form->SetBind(true, $row);

		// Check-out the note
		$class->CheckoutNote($id, $user->GetId());

		require_once($this->GetOutput('form_edit_note.php'));
	}

	public function DisplayNote($id, $dir = 0, $v = 0)
	{
	    $editor = JFactory::getEditor();
	    $user   = PFuser::GetInstance();
		$class  = new PFfilemanagerClass();
		$form   = new PFform();

		$row = $class->LoadNote($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('display_note', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		$ws_title = PFformat::WorkspaceTitle();
		$form->SetBind(true, $row);

		require_once($this->GetOutput('display_note.php'));
	}

	public function DisplayEditComment($id)
	{
		$this->DisplayNote($id);
	}

	public function DisplayNoteVersions($id, $dir = 0)
	{
	    $config = PFconfig::GetInstance();
	    $user   = PFuser::GetInstance();
	    $load   = PFload::GetInstance();

		$class  = new PFfilemanagerClass();
		$form   = new PFform();
		$rows   = $class->LoadNoteVersions($id);
		$row    = $class->LoadNote($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('list_note_versions', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		$ws_title = PFformat::WorkspaceTitle();
        $use_compare = $config->Get('note_compare', 'filemanager_pro');
        $can_compare = $user->Access('form_compare_note', 'filemanager_pro');

		$table = new PFtable(array(),array(),'id','ASC');

		$form->SetBind(true, 'REQUEST');

		$load->SectionCSS('filemanager_pro.css', 'filemanager_pro');
		$load->SectionJS('filemanager_pro.js', 'filemanager_pro');

		require_once($this->GetOutput('list_note_versions.php'));
	}

    public function DisplayFileVersions($id, $dir = 0)
	{
	    if(defined('PF_DEMO_MODE')) return false;

	    $user  = PFuser::GetInstance();
	    $load  = PFload::GetInstance();

		$class = new PFfilemanagerClass();
		$form  = new PFform();
		$rows  = $class->LoadFileVersions($id);
		$row   = $class->LoadFile($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('list_file_versions', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		$ws_title = PFformat::WorkspaceTitle();
		$table    = new PFtable(array(),array(),'id','ASC');

		$form->SetBind(true, 'REQUEST');

		$load->SectionCSS('filemanager_pro.css', 'filemanager_pro');
		$load->SectionJS('filemanager_pro.js', 'filemanager_pro');

		require_once($this->GetOutput('list_file_versions.php'));
	}

    public function DisplayCompareNote($id, $n1 = 0, $n2 = 0, $dir = 0)
    {
        $db     = PFdatabase::GetInstance();
        $config = PFconfig::GetInstance();
        $user   = PFuser::GetInstance();

		$class = new PFfilemanagerClass();
		$form  = new PFform();
		$row   = $class->LoadNote($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_compare_note', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

        if(!$n1) {
            $query = "SELECT MAX(id) FROM #__pf_note_versions WHERE note_id = '$id'";
                   $db->setQuery($query);
                   $n1 = (int) $db->loadResult();
        }

        if(!$n2) $n2 = $n1;

        $row1 = $class->LoadNoteVersion($n1);
        $row2 = $class->LoadNoteVersion($n2);

        // Check if record exists
		if(!is_object($row1) || !is_object($row2)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Compare notes
        $compare_data = $class->CompareVersions($row1->content, $row2->content);
        $content1 = $compare_data[0];
        $content2 = $compare_data[1];
        $missing  = $compare_data[2];
        $new      = $compare_data[3];

        // Line colors
        $new_line_color     = $config->Get('color_new', 'filemanager_pro');
        $new_line_bg        = $config->Get('bg_new', 'filemanager_pro');
        $missing_line_bg    = $config->Get('bg_missing', 'filemanager_pro');
        $missing_line_color = $config->Get('color_missing', 'filemanager_pro');

        $ws_title = PFformat::WorkspaceTitle();
        $form->SetBind(true, 'REQUEST');

        require_once($this->GetHelper('filemanager_pro'));
        require_once($this->GetOutput('form_compare_note.php'));
    }

	public function SaveFolder($dir = 0)
	{
		$class = new PFfilemanagerClass();

		// Check folder access
        if($dir) {
            $folder = $class->LoadFolder($dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		if(!$class->SaveFolder($dir)) {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_FOLDER_E_SAVE');
			return false;
		}

		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_FOLDER_S_SAVE');
		return true;
	}

	public function SaveNote($dir = 0)
	{
		$class = new PFfilemanagerClass();

		// Check folder access
        if($dir) {
            $folder = $class->LoadFolder($dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		if(!$class->SaveNote($dir)) {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_NOTE_E_SAVE');
			return false;
		}

		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_NOTE_S_SAVE');
		return true;
	}

	public function UploadFiles($files, $dir = 0)
	{
	    if(defined('PF_DEMO_MODE')) return false;

		$class   = new PFfilemanagerClass();
        $is_ajax = JRequest::getVar('render');
        $is_ajax = ($is_ajax == 'section_ajax') ? true : false;

        // Ajax upload
        if($is_ajax) {
            // Include helper class
            require_once($this->GetHelper('filemanager_pro'));

            $user   = PFuser::GetInstance();
            $dest   = PFfilemanagerHelper::GetUploadPath();
            $upload = New AjaxUpload('qqfile');
            $result = $upload->HandleFile($dest, $user->GetWorkspace());

            if(array_key_exists('success', $result)) {
                // Successful upload - Register new file in db
                $db     = PFdatabase::GetInstance();
                $config = PFconfig::GetInstance();

                $f      = $result['file'];
                $name   = $f['name'];
                $prefix = $f['prefix'];
                $size   = $f['size'];

                $uid  = $user->GetId();
                $p    = $user->GetWorkspace();
                $msg  = $result;
                $desc = '';
                $now  = time();

                if((int)$config->Get('file_vc', 'filemanager_pro')) {
                    // Search for existing file name
                    $v_id = PFfilemanagerHelper::FileNameExists($name, $dir);

                    if($v_id) {

                        $v_id2 = $class->CreateFileVersion($v_id, $name, $prefix, '', $user->GetId(), $size, time());
                        if($v_id2) {
                            // Add properties
                            $query = "INSERT INTO #__pf_file_properties VALUES("
                                   . "\n NULL, '$v_id2', '0', '0', '0', '0', '0', ''"
                                   . "\n )";
            	                   $db->setQuery($query);
            	                   $db->query();

                            $id = $db->insertid();
    			            if(!$id) $msg = array('error'=> $db->getErrorMsg());

                            // Update main record
                            $q_name = $db->quote($name);
                            $q_size = $db->quote($size);
                            $q_pfx  = $db->quote($prefix);

                            $query = "UPDATE #__pf_files SET name = $q_name description = '',"
                                   . "\n filesize = $q_size, edate = ".$db->quote(time())." "
		                           . "\n prefix = $q_pfx WHERE id = ".$db->quote($v_id2);
		                           $db->setQuery($query);
		                           $db->query();

                            $id = $db->insertid();
    			            if(!$id) $msg = array('error'=> $db->getErrorMsg());
                        }

                        ob_end_clean();
                        ob_end_clean();
                        echo htmlspecialchars(json_encode($msg), ENT_NOQUOTES);
                        die();
                    }
                }

                // Insert db record
    			$query = "INSERT INTO #__pf_files VALUES("
                       . "\n NULL, ".$db->quote($name).", '".$prefix."',"
                       . "\n '', ".$db->quote($uid).","
    			       . "\n ".$db->quote($p).", ".$db->quote($dir).","
                       . "\n ".$db->quote($size).", ".$db->quote($now).", "
                       . "\n ".$db->quote($now).")";
    			       $db->setQuery($query);
    			       $db->query();

    			$id = $db->insertid();
    			if(!$id) $msg = array('error'=> $db->getErrorMsg());

                // Add properties
                if($id) {
                    $query = "INSERT INTO #__pf_file_properties VALUES("
                           . "\n NULL, '$id', '0', '0', '0', '0', '0', ''"
                           . "\n )";
    	                   $db->setQuery($query);
    	                   $db->query();

                    // Create new version
    	            if((int)$config->Get('file_vc', 'filemanager_pro')) {
    		            $class->CreateFileVersion($id,$name,$prefix,$desc,$uid,$size,$now);
    	            }
                }

                ob_end_clean();
                ob_end_clean();
                echo htmlspecialchars(json_encode($msg), ENT_NOQUOTES);
                die();
            }
            else {
                ob_end_clean();
                ob_end_clean();
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                die();
            }
        }

        /**
        * Default upload below
        **/

        // Check folder access
        if($dir) {
            $folder = $class->LoadFolder($dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		if(!$class->UploadFiles($files, $dir)) {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_FILE_E_UPLOAD');
			return false;
		}

		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_FILE_S_UPLOAD');
		return true;
	}

	public function SaveComment($id, $dir)
	{
	    $class = new PFfilemanagerClass();

	    // Check folder access
        if($dir) {
            $folder = $class->LoadFolder($dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

        if(defined('PF_COMMENTS_PROCESS')) {
            $user = PFuser::GetInstance();
        	$comments = new PFcomments();
        	$comments->init('notes', $id);
        	$title   = JRequest::getVar('title');
        	$content = JRequest::getVar('ctext');

            if(!$comments->Save($title, $content, $user->GetId())) {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'MSG_COMMENT_E_SAVE');
			    return false;
		    }
		    else {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'MSG_COMMENT_S_SAVE');
			    return true;
		    }
        }
        else {
        	$this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'MSG_COMMENT_E_SAVE');
        	return false;
        }
	}

	public function UpdateFolder($id, $dir = 0)
	{
	    $user = PFuser::GetInstance();

		$class = new PFfilemanagerClass();
		$row   = $class->LoadFolder($id);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_folder', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if(!$class->CheckFolderAccess($row)) {
            $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
            return false;
        }

		if(!$class->UpdateFolder($id)) {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_E_UPDATE');
			return false;
		}

		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_S_UPDATE');
        return true;
	}

	public function UpdateFile($id, $file, $dir)
	{
	    if(defined('PF_DEMO_MODE')) return false;

	    $user  = PFuser::GetInstance();
		$class = new PFfilemanagerClass();

        $row = $class->LoadFile($id);

        // Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_file', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

        // Update file
		if(!$class->UpdateFile($id, $file, $dir)) {
		    $class->CheckinFile($id);
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_E_UPDATE');
			return false;
		}

		$class->CheckinFile($id);
		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_S_UPDATE');
		return true;
	}

	public function UpdateNote($id, $dir)
	{
	    $user  = PFuser::GetInstance();
		$class = new PFfilemanagerClass();

        $row = $class->LoadNote($id);

        // Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('form_edit_note', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		// Update the note
		if(!$class->UpdateNote($id)) {
		    $class->CheckinNote($id);
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_E_UPDATE');
			return false;
		}

		$class->CheckinNote($id);
		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_S_UPDATE');
		return true;
	}

	public function UpdateComment($id, $cid)
	{
        if(defined('PF_COMMENTS_PROCESS')) {
        	$comments = new PFcomments();
        	$comments->Init('notes', $id);
        	$title   = JRequest::getVar('title');
        	$content = JRequest::getVar('ctext');

            if(!$comments->Update($title, $content, $cid)) {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_E_UPDATE');
			    return false;
		    }
		    else {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_S_UPDATE');
			    return true;
		    }
        }
        else {
        	$this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_E_UPDATE');
        	return false;
        }
	}

	public function Delete($dir = 0, $folder = array(), $note = array(), $file = array())
	{
	    require_once($this->GetHelper('filemanager_pro'));

		$class   = new PFfilemanagerClass();
		$folders = array();

		// Delete folders
		if(!empty($folder)) {
		    $folders = $class->DeleteFolder($dir, $folder);

		    if($folders === false) {
                $this->SetRedirect("section=filemanager_pro&dir=$dir", 'FM_E_DELFOLDERS');
                return false;
            }
		}

        // Delete notes
		if(!$class->DeleteNotes($note, $folders, $dir)) {
            $this->SetRedirect("section=filemanager_pro&dir=$dir", 'FM_E_DELNOTES');
            return false;
        }

        // Delete files
		if(!$class->DeleteFiles($file, $folders, $dir)) {
            $this->SetRedirect("section=filemanager_pro&dir=$dir", 'FM_E_DELFILES');
            return false;
        }

		$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_S_DELETE');
		return true;
	}

	public function DeleteComment($id, $cid)
	{
        if(defined('PF_COMMENTS_PROCESS')) {
        	$comments = new PFcomments();
        	$comments->init('notes', $id);

            if(!$comments->Delete($cid)) {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_E_DELETE');
			    return false;
		    }
		    else {
			    $this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_S_DELETE');
			    return true;
		    }
        }
        else {
        	$this->SetRedirect("section=filemanager_pro&task=display_note&id=$id", 'COMMENT_E_DELETE');
        	return false;
        }
	}

	public function DownloadFile($id, $dir, $v)
	{
	    if(defined('PF_DEMO_MODE')) return false;

		jimport('joomla.filesystem.file');

		$class  = new PFfilemanagerClass();
        $config = PFconfig::GetInstance();
        $user   = PFuser::GetInstance();

		$row = $class->LoadFile($id, $v);

		// Check if record exists
		if(!is_object($row)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'MSG_ITEM_NOT_FOUND');
            return false;
        }

        // Double check access
        if(!$user->Access('task_download', 'filemanager_pro', $row->author)) {
            $this->SetRedirect('section=filemanager_pro&dir='.$dir, 'NOT_AUTHORIZED');
            return false;
        }

        // Check folder access
        if($row->dir) {
            $folder = $class->LoadFolder($row->dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=".$dir, 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		$name    = JFile::makeSafe($row->name);
		$prefix1 = "project_".$row->project;
		$prefix2 = $row->prefix;
		$upath   = $config->Get('upload_path', 'filemanager_pro');

		$download_path = JPath::clean(JPATH_ROOT.DS.$upath.DS.$prefix1.DS.$prefix2.strtolower($name));

		if(file_exists($download_path)) {
            ob_end_clean();
            ob_end_clean();
			header("Content-Type: APPLICATION/OCTET-STREAM");
			header("Content-Length: ".filesize($download_path));
			header("Content-Disposition: attachment; filename=\"$name\";");
			header("Content-Transfer-Encoding: Binary");

			if(function_exists('readfile')) {
			    readfile($download_path);
			}
            else {
                echo file_get_contents($download_path);
            }
			die();
		}
		else {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_FILE_NOT_FOUND');
			return false;
		}
	}

	public function Move($dir = 0, $mfolders = array(), $mnotes = array(), $mfiles = array())
	{
		$class = new PFfilemanagerClass();

		// Check folder access
        if($dir) {
            $folder = $class->LoadFolder($dir);
            if(!$class->CheckFolderAccess($folder)) {
                $this->SetRedirect("section=filemanager_pro&dir=0", 'MSG_RES_RESTRICTED');
                return false;
            }
        }

		if(!$class->Move($dir, $mfolders, $mfiles, $mnotes)) {
			$this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_E_MOVE');
			return false;
		}

        $this->SetRedirect("section=filemanager_pro&dir=$dir", 'MSG_S_MOVE');
		return true;
	}
}
?>