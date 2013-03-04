<?php
/**
* $Id: task_details.php 837 2010-11-17 12:03:35Z eaxs $
* @package   Projectfork
* @copyright Copyright (C) 2006-2010 Tobias Kuhn. All rights reserved.
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Projectfork.
*
* Projectfork is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Projectfork is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Projectfork.  If not, see <http://www.gnu.org/licenses/gpl.html>.
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
		   
    $query = "SELECT tu.user_id, u.id, u.name FROM #__pf_task_users AS tu"
	       . "\n RIGHT JOIN #__users AS u ON u.id = tu.user_id"
	       . "\n WHERE tu.task_id = ".$db->Quote($id)
           . "\n GROUP BY tu.user_id ORDER BY u.name ASC";
	       $db->setQuery($query);
	       $urows = $db->loadObjectList();

	if(is_array($urows)) {
	    foreach($urows AS $assigned)
        {
            $avatar = "";
            $avatar = PFavatar::Display($assigned->user_id);
            
            // format responsible user
		    $users .= "<div>"
                   . $avatar."</div>
                   <strong>".htmlspecialchars($assigned->name)."</strong>";
        }
    }
    
    if($users == "") $users = PFformat::Lang('NOT_SET');
	
     // Format deadline
	 if($row->edate) {
	     $edate = PFformat::ToDate($row->edate);
	 }
	 else {
	     $edate = PFformat::Lang('NOT_SET');
	 }
	 
     $avatar = PFavatar::Display($row->author);
	 ?>
	 <table class="admintable">
	     <tr>
	         <td class="key" width="100"><?php echo PFformat::Lang('CREATED_ON');?></td>
		     <td><?php echo PFformat::ToDate($row->cdate);?></td>
	     </tr>
	     <tr>
	         <td class="key" width="100"><?php echo PFformat::Lang('DEADLINE');?></td>
	         <td><?php echo $edate;?></td>
	     </tr>
	     <tr>
	         <td class="key" width="100"><?php echo PFformat::Lang('PROGRESS');?></td>
			 <td><?php echo $progr->name; ?></td>
	     </tr>
	     <tr>
	         <td class="key" width="100"><?php echo PFformat::Lang('PRIORITY');?></td>
	         <td><?php echo PFtasksHelper::RenderPriority($row->priority); ?></td>
	     </tr>
	     <tr>
	         <td class="key" width="100" valign="top"><?php echo PFformat::Lang('AUTHOR');?></td>
	         <td>
		         <div class="pf_avatar"><?php echo $avatar;?></div>
		         <strong><?php echo htmlspecialchars($row->name);?></strong>
	         </td>
	     </tr>
	     <tr>
	         <td class="key" width="100" valign="top"><?php echo PFformat::Lang('ASSIGNED_TO');?></td>
	         <td><?php echo $users;?></td>
	     </tr>
   <tr>
      <td class="key" width="100" valign="top"><?php echo PFformat::Lang('POD');?></td>
      <td>
         <strong><?php echo $row->pod;?></strong>  
      </td>
   </tr>
<?php

switch ($row->typology)
{
case 1: // Voltura
	echo '      
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Voltura</strong></td>
		</tr>';
   break;
	
case 2: // Nuova fornitura
	echo '      
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Nuova Fornitura</strong></td>
		</tr>		
		<tr>
			<td class="key" width="100" valign="top">Indirizzo/Localita\'/Cap</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>
		<tr>
			<td class="key" width="100" valign="top">Potenza</td>
			<td><strong>'. $row->campo2 .'</strong></td>
		</tr>
		<tr>
			<td class="key" width="100" valign="top">Tensione</td>
			<td><strong>'. $row->campo3 .'</strong></td>
		</tr>';		
   break;

case 3: // Spostamento
	echo '      
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Spostamento</strong></td>
		</tr>		
		<tr>
			<td class="key" width="100" valign="top">Oltre 10 metri</td>
			<td><strong>';
	if ($row->campo1 == 'Si') {
		echo "Si";
	}
	else {
		echo "No";
	}

	echo '
		</strong></td>
		</tr>';
   break;

case 4: // Attivazione
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Attivazione</strong></td>
		</tr>
		<tr>
			<td class="key" width="100" valign="top">Definitiva/Temporanea</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>';

			if ( $row->campo1 == "Temporanea" ) {
				echo '<tr>
						<td class="key" width="100" valign="top">Da</td>
						<td><strong>'. $row->campo2 .'</strong></td>
					</tr>
					<tr>
						<td class="key" width="100" valign="top">A</td>
						<td><strong>'. $row->campo3 .'</strong></td>
					</tr>';				
			}
		
   break;
 case 5: // Disattivazione
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Disattivazione</strong></td>
		</tr>
		<tr>
			<td class="key" width="100" valign="top">Definitiva/Temporanea</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>';

			if ( $row->campo1 == "Definitiva" ) {
				echo '<tr>
						<td class="key" width="100" valign="top">Rimozione contatore</td>
						<td><strong>'. ($row->campo2 == "Si" ? "Si" : "No") .'</strong></td>
					</tr>
					<tr>
						<td class="key" width="100" valign="top">Rimozione presa</td>
						<td><strong>'. ($row->campo3 == "Si" ? "Si" : "No") .'</strong></td>
					</tr>';		
			}
			else {
				echo '<tr>
						<td class="key" width="100" valign="top">Da</td>
						<td><strong>'. $row->campo2 .'</strong></td>
					</tr>
					<tr>
						<td class="key" width="100" valign="top">A</td>
						<td><strong>'. $row->campo3 .'</strong></td>
					</tr>';				
			}
		
   break;
case 6: // Variazione Tensione
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Variazione Tensione</strong></td>
		</tr>		
		<tr>
			<td class="key" width="100" valign="top">Tensione richiesta</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>';		
   break;
 case 7: // Variazione Potenza
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Variazione Potenza</strong></td>
		</tr>		
		<tr>
			<td class="key" width="100" valign="top">Potenza richiesta</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>';		
   break;
case 8: // Variazione Tipologia d'uso
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Variazione Tipologia d\'uso</strong></td>
		</tr>		
		<tr>
			<td class="key" width="100" valign="top">Tipologia d\'uso</td>
			<td><strong>'. $row->campo1 .'</strong></td>
		</tr>';		
   break;   
case 9: // Altro
	echo '   
		<tr>
			<td class="key" width="100" valign="top">Tipologia</td>
			<td><strong>Altro</strong></td>
		</tr>';			
   break;   
}
?>
	 </table>
	 <?php
}
?>