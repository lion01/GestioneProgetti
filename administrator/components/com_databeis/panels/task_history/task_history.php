<?php
/**
* $Id: task_details.php 837 2010-11-17 12:03:35Z eaxs $
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

$id    = (int) JRequest::getVar('id');
$db    = PFdatabase::GetInstance();
$load  = PFdatabase::GetInstance();
$load  = PFload::GetInstance();
$form  = new PFform();
$users = "";

require_once( PFobject::GetHelper('tasks') );

if($id) {
	// Include task class
	if(!class_exists('PFtasksClass')) require_once($load->Section('tasks.class.php', 'tasks'));
	$class = new PFtasksClass();
	
	$query = "SELECT t.*, u.name FROM #__pf_tasks AS t"
	       . "\n LEFT JOIN #__users AS u ON u.id = t.author"
	       . "\n WHERE t.id = ".$db->Quote($id);
	       $db->setQuery($query);
	       $row = $db->loadObject();

	$query = "SELECT * FROM #__pf_progress WHERE id = ".$row->progress;
	       $db->setQuery($query);
	       $progr = $db->loadObject();		   
	$query = "SELECT * FROM #__pf_progressing"			. "\n  WHERE task_id = ".$db->Quote($id);	       $db->setQuery($query);	       $steps = $db->loadObjectList();	   
    $query = "SELECT tu.user_id, u.id, u.name FROM #__pf_task_users AS tu"
	       . "\n RIGHT JOIN #__users AS u ON u.id = tu.user_id"
	       . "\n WHERE tu.task_id = ".$db->Quote($id)
           . "\n GROUP BY tu.user_id ORDER BY u.name ASC";
	       $db->setQuery($query);
	       $urows = $db->loadObjectList();	?>	   
	 <table class="admintable">		<tr>			<td>Fase</td>			<td>Data</td>			<td>Utente</td>		</tr><?php		if(is_array($steps)) {	    foreach($steps AS $stepp)        {			$query = "SELECT * FROM #__pf_progress WHERE id = ".$stepp->step;	       $db->setQuery($query);	       $progre = $db->loadObject();		   			$query = "SELECT u.name FROM #__pf_progressing AS pr"	       . "\n RIGHT JOIN #__users AS u ON u.id = pr.user_id";		   		   	       $db->setQuery($query);	       $uname = $db->loadObject();   ?>		   			<tr>			<td><?php echo $progre->name;?></td>						<td><?php echo PFformat::ToDate($stepp->cdate);?></td>						<td><?php echo $uname->name;?></td>			</tr><?php	        }    }

	 ?>


	 </table>
	 <?php
}
?>