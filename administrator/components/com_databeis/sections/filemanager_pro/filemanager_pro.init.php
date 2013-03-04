<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


// Get core objects
$core = PFcore::GetInstance();

// Get user input
$id    = (int) JRequest::getVar('id');
$dir   = (int) JRequest::getVar('dir');
$c_id  = (int) JRequest::getVar('cid');
$v     = (int) JRequest::getVar('v');
$n1    = (int) JRequest::getVar('n1');
$n2    = (int) JRequest::getVar('n2');
$files = JRequest::getVar('file', array(), 'files');

$file  = JRequest::getVar('file', array(), 'files');

$mfolder = JRequest::getVar('folder', array());
$mnote   = JRequest::getVar('note', array());
$mfile   = JRequest::getVar('file', array());

// New controller
$controller = new PFfileController();

switch( $core->GetTask() )
{
	default:
	case 'list_directory':	
		$controller->DisplayList();
		break;
		
	case 'list_move':
		$controller->DisplayMove($dir, $mfolder, $mnote, $mfile);
		break;
		
	case 'list_note_versions':
        $controller->DisplayNoteVersions($id, $dir = 0);
		break;

    case 'list_file_versions':
        $controller->DisplayFileVersions($id, $dir = 0);
		break;
		
	case 'form_new_folder':
		$controller->DisplayNewFolder($dir);
		break;
		
	case 'form_new_file':
        $controller->DisplayNewFile($dir);
		break;
		
	case 'form_new_note':
        $controller->DisplayNewNote($dir);
		break;
		
	case 'form_edit_folder':
        $controller->DisplayEditFolder($id, $dir);
		break;
		
	case 'form_edit_note':
        $controller->DisplayEditNote($id, $dir);
		break;
		
	case 'form_edit_file':
        $controller->DisplayEditFile($id, $dir);
		break;

    case 'form_compare_note':
        $controller->DisplayCompareNote($id, $n1, $n2, $dir);
        break;
		
	case 'task_delete':
        $controller->Delete($dir, $mfolder, $mnote, $mfile);
		break;
		
	case 'task_save_folder':
        $controller->SaveFolder($dir);
		break;
		
	case 'task_save_note':
        $controller->SaveNote($dir);
		break;
		
	case 'task_save_file':
        $controller->UploadFiles($files, $dir);
		break;
		
	case 'task_update_folder':
        $controller->UpdateFolder($id, $dir);
		break;
		
	case 'task_update_note':
        $controller->UpdateNote($id, $dir);
		break;
		
	case 'task_update_file':
        $controller->UpdateFile($id, $file, $dir);
		break;
		
	case 'task_download':
        $controller->DownloadFile($id, $dir, $v);
		break;
		
	case 'task_move':
        $controller->Move($dir, $mfolder, $mnote, $mfile);
		break;	
		
	case 'display_note':
        $controller->DisplayNote($id, $dir, $v);
		break;
		
	case 'task_save_comment':
		$controller->SaveComment($id, $dir);
		break;
		
	case 'form_edit_comment':
		$controller->DisplayEditComment($id);
		break;
		
	case 'task_update_comment':
		$controller->UpdateComment($id, $c_id);
		break;

	case 'task_delete_comment':
		$controller->DeleteComment($id, $c_id);
		break;
}
?>