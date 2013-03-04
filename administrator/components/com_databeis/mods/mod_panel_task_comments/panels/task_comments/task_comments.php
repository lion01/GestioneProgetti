<?php
/**
* $Id: task_comments.php 576 2010-07-04 10:45:40Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Databeis is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Databeis.  If not, see <http://www.gnu.org/licenses/gpl.html>.
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

if(defined('PF_COMMENTS_PROCESS')) {

	$id  = (int) JRequest::getVar('id');
	$cid = (int) JRequest::getVar('cid');
	$ls  = (int) JRequest::getVar('limitstart');
    $k   = urlencode(JRequest::getVar('keyword'));
    
	$config = PFconfig::GetInstance();
	$db     = PFdatabase::GetInstance();
	$core   = PFcore::GetInstance();
	$com    = new PFcomments();
	$task   = $core->GetTask();

	$query = "SELECT title FROM #__pf_tasks WHERE id = '$id'";
	       $db->setQuery($query);
	       $title = $db->loadResult();
	       
	$com->Init('tasks', $id);

	if($task == 'form_edit_comment' && $cid) {
		//$pf_form = new PFform('adminform', 'index.php?option=com_databeis&section=tasks&task=task_update_comment&id='.$id.'&cid='.$cid);
		$pf_form = new PFform('adminform', 'index.php?option=com_databeis&section=tasks&task=task_update_comment&id='.$id.'&cid='.$cid,  'post', 'enctype="multipart/form-data"');
		$cform   = $com->RenderEdit($cid);
	}
	else {
		//$pf_form = new PFform('adminform', 'index.php?option=com_databeis&section=tasks&task=task_save_comment&id='.$id);
		$pf_form = new PFform('adminform', 'index.php?option=com_databeis&section=tasks&task=task_save_comment&id='.$id,  'post', 'enctype="multipart/form-data"');
		$cform   = $com->RenderNew($title);
	}
	
	$list = $com->RenderList();
	
	if((int)$config->Get('use_comments', 'tasks') && $id) {
		echo $pf_form->Start();
	    echo $cform;
	    echo $list;
        echo $pf_form->HiddenField('id', $id);
	    echo $pf_form->End();
	}
}
?>