<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


// Load core objects
$core   = PFcore::GetInstance();
$config = PFconfig::GetInstance();
$form   = new PFform('adminForm_subnav');
$sobj   = $core->GetSectionObject();

// Get config settings
$use_compare = (int) $config->Get('note_compare', 'filemanager_pro');
$use_ab      = (int) $config->Get('use_addressbar', 'filemanager_pro');

// Get user input
$dir = (int) JRequest::getVar('dir', 0);
$v   = (int) JRequest::getVar('v', 0);
$id  = (int) JRequest::getVar('id', 0);
$ob  = JRequest::getVar('ob', 'id');
$od  = JRequest::getVar('od', 'ASC');

$form->SetBind(true, 'REQUEST');
      
switch( $core->GetTask() )
{
	default:
	case 'list_directory':
	    echo $form->Start();
		?>
        <div class="pf_navigation">
           <ul>
              <li class="btn pf_new_folder"><?php echo $form->NavButton('NEW_FOLDER', 'section=filemanager_pro&dir='.$dir.'&task=form_new_folder', 'TT_NEW_FOLDER', 'form_new_folder');?></li>
              <?php if(!defined('PF_DEMO_MODE')) { ?>
                  <li class="btn pf_new_file"><?php echo $form->NavButton('NEW_FILE', 'section=filemanager_pro&dir='.$dir.'&task=form_new_file', 'TT_UPLOAD_FILE', 'form_new_file');?></li>
              <?php } ?>
              <li class="btn pf_new_note"><?php echo $form->NavButton('NEW_NOTE', 'section=filemanager_pro&dir='.$dir.'&task=form_new_note', 'TT_NEW_NOTE', 'form_new_note');?></li>
              <li class="btn pf_move"><?php echo $form->NavButton('MOVE', 'javascript:list_move()', 'TT_MOVE', 'list_move');?></li>
              <li class="btn pf_delete"><?php echo $form->NavButton('DELETE', 'javascript:task_delete()', 'TT_DELETE', 'task_delete');?></li>
           </ul>
        </div>
        <div class="pfl_search">
              <span>
                 <?php echo PFformat::Lang('SEARCH');?>
                 <?php echo $form->InputField('keyword');?>
              </span>
              <span class="btn"><?php echo $form->NavButton('OK', "javascript:document.adminForm_subnav.submit();");?></span>
        </div>
        <?php if($use_ab ) { ?>
        <div class="pf_addressbar">
              <?php echo PFfilemanagerHelper::RenderAddressBar($dir); ?>
        </div>
		<?php
		}
		echo $form->HiddenField('option');
        echo $form->HiddenField('section');
        echo $form->HiddenField('task');
        echo $form->HiddenField('dir');
        echo $form->HiddenField('ob', $ob);
        echo $form->HiddenField('od', $od);
        echo $form->End();
		break;
		
	case 'list_move':
		?>
		<div class="pf_navigation">
           <ul>
              <li class="btn pf_move"><?php echo $form->NavButton('MOVE', "javascript:submitbutton('task_move')", 'TT_MOVE');?></li>
              <li class="btn pf_cancel"><?php echo $form->NavButton('CANCEL', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
           </ul>
        </div>
        <?php if($use_ab ) { ?>
        <div class="pf_addressbar">
              <?php echo PFfilemanagerHelper::RenderAddressBar($dir); ?>
        </div>
		<?php
		}
		break;	
		
	case 'form_new_folder':
	case 'form_new_file':
	case 'form_new_note':
		$task = explode('_', $core->GetTask());
		$task = array_pop($task);
        ?>
        <div class="pf_navigation">
           <ul>
              <li class="btn pf_save"><?php echo $form->NavButton('SAVE', "javascript:task_save_$task()", 'TT_CREATE_'.strtoupper($task), 'task_save_'.$task);?></li>
              <?php if($task == 'file') { ?>
              <li class="btn pf_new"><?php echo $form->NavButton('ADD', "javascript:addFile('add_file', 'file_container');");?></li>
              <?php } ?>
              <li class="btn pf_cancel"><?php echo $form->NavButton('CANCEL', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
           </ul>
        </div>
        <?php
		break;
		
	case 'form_edit_folder':
	case 'form_edit_note':
	case 'form_edit_file':
	    $checkin = '';
	    $task = $core->GetTask();
	    if($task == 'form_edit_note') $checkin = "&checkin_note=$id";
	    if($task == 'form_edit_file') $checkin = "&checkin_file=$id";
		$task = explode('_', $core->GetTask());
		$task = array_pop($task);
        ?>
        <div class="pf_navigation">
           <ul>
              <li class="btn pf_save"><?php echo $form->NavButton('SAVE', "javascript:task_update_$task()", 'TT_UPDATE', 'task_update_'.$task);?></li>
              <li class="btn pf_cancel"><?php echo $form->NavButton('CANCEL', 'section=filemanager_pro&dir='.$dir.$checkin, 'TT_BACK');?></li>
           </ul>
        </div>
        <?php
		break;
		
	case 'display_note':
	case 'form_edit_comment':
		if($v && $use_compare) {
		    $db = PFdatabase::GetInstance();
            $query = "SELECT MAX(id) FROM #__pf_note_versions"
                   . "\n WHERE note_id = '$id'";
                   $db->setQuery($query);
                   $n2 = $db->loadResult();
        }
        ?>
        <div class="pf_navigation">
            <ul>
                <li class="btn pf_back"><?php echo $form->NavButton('BACK', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
                <li class="btn pf_edit"><?php echo $form->NavButton('EDIT', 'section=filemanager_pro&dir='.$dir.'&task=form_edit_note&id='.$id, 'TT_EDIT', 'form_edit_note');?></li>
                <li class="btn pf_new_note"><?php echo $form->NavButton('PFL_LIST_VERSIONS', 'section=filemanager_pro&dir='.$dir.'&id='.$id.'&task=list_note_versions', 'PFL_NLV_BD', 'list_note_versions');?></li>
                <?php if($v && $use_compare) { ?>
                    <li class="btn pf_move"><?php echo $form->NavButton('COMPARE', 'section=filemanager_pro&dir='.$dir.'&id='.$id.'&task=form_compare_note&n1='.$v.'&n2='.$n2, 'COMPARE_DESC', 'form_compare_note');?></li>
                <?php } ?>
            </ul>
        </div>
		<?php
		break;


    case 'form_compare_note':
		?>
        <div class="pf_navigation">
            <ul>
               <li class="btn pf_back"><?php echo $form->NavButton('BACK', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
               <li class="btn pf_edit"><?php echo $form->NavButton('EDIT', 'section=filemanager_pro&dir='.$dir.'&task=form_edit_note&id='.$id, 'TT_EDIT');?></li>
               <li class="btn pf_new_note"><?php echo $form->NavButton('PFL_LIST_VERSIONS', 'section=filemanager_pro&dir='.$dir.'&id='.$id.'&task=list_note_versions', 'PFL_NLV_BD');?></li>
            </ul>
        </div>
		<?php
		break;

    case 'list_note_versions':
        ?>
        <div class="pf_navigation">
            <ul>
               <li class="btn pf_back"><?php echo $form->NavButton('BACK', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
               <li class="btn pf_edit"><?php echo $form->NavButton('EDIT', 'section=filemanager_pro&dir='.$dir.'&task=form_edit_note&id='.$id, 'TT_EDIT');?></li>
            </ul>
        </div>
		<?php
        break;

    case 'list_file_versions':
        ?>
        <div class="pf_navigation">
            <ul>
               <li class="btn pf_back"><?php echo $form->NavButton('BACK', 'section=filemanager_pro&dir='.$dir, 'TT_BACK');?></li>
               <li class="btn pf_edit"><?php echo $form->NavButton('EDIT', 'section=filemanager_pro&dir='.$dir.'&task=form_edit_file&id='.$id, 'TT_EDIT');?></li>
            </ul>
        </div>
		<?php
        break;
}
?>