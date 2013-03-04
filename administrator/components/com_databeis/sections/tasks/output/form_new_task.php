<?php
/**
* $Id: form_new_task.php 864 2011-03-21 06:02:52Z angek $
* @package    Projectfork
* @subpackage Tasks
* @copyright  Copyright (C) 2006-2010 Tobias Kuhn. All rights reserved.
* @license    http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Projectfork.
*
* Projectfork is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License License as published by
* the Free Software Foundation, either version 3 of the License,
* or any later version.
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

?>

<script src="/jquery-1.9.1.min.js"></script>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	if(document.adminForm.title.value == "") {
		alert("<?php echo PFformat::Lang('V_TITLE');?>");
	}
	else {
		<?php if($use_editor && !defined('PF_DEMO_MODE')) { echo $editor->save( 'text' ); } ?>
        	submitform( pressbutton );
	}
}
function switch_deadline(ch)
{
	var el = document.getElementById('dealine_table');
	if(ch) {
		el.style.display = "";
	}
	else {
		el.style.display = "none";
	}
}

function switch_editor(ched)
{
	var ed = document.getElementById('editor');
	if(ched) {
		ed.style.display = "";
	}
	else {
		ed.style.display = "none";
	}
}

function callFromDialog(id,data,tensione,potenza){
	document.getElementById(id).value = data;
	if ( document.getElementById("tensione") != null ) {
		document.getElementById("tensione").value = tensione;
	}
	if ( document.getElementById("potenza") != null ) {
		document.getElementById("potenza").value = potenza;
	}	
}

function choose(id, commessa, tiporicerca){
	var URL = "http://gestioneprogetti.altervista.org/administrator/components/com_databeis/sections/tasks/output/pod.php?id=" + id + "&pod=" + document.getElementById('ricerca').value + "&commessa=" + commessa + "&tiporicerca=" + tiporicerca;
	window.open(URL,"mywindow","menubar=1,resizable=1,width=900,height=400, scrollbars=1")
}

function switch_typo()
{
	/*
		var typ = parseInt(document.getElementById("typolo").value);
		for ( var i = 1; i < 10; i ++ )
		{
			document.getElementById("typo_table" + i).style.display = "none";
			document.getElementsByName("pod")[i-1].value = "";
		//	document.getElementsByName("campo1")[i-1].value = "";
		}
			document.getElementById("typo_table" + typ).style.display = "";
*/ /*
			if ( document.getElementsByClassName("rigaclass").length > 0 ) 
			{
				var rigaclasses = document.getElementsByClassName("rigaclass");
				while ( rigaclasses.length > 0 ) {
					rigaclasses[0].parentNode.removeChild(rigaclasses[0]);
				}
			}
	*/

			$('.rigaclass').remove();
	
		//	var asd = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($form->InputField('campo1*', '', 'size="40" maxlength="124"'))); ?>";
		//	var phpp = '<?php echo $form->InputField('campo1*', '', 'size="40" maxlength="124"');?>';
		
			var selezindex = document.getElementById("typolo").selectedIndex;
			var tipologia = document.getElementById("typolo").options[selezindex].text;
			document.getElementById('tempo').style.display = "none";			
			document.getElementById('campo2a').setAttribute("name", "campo2a");
			document.getElementById('campo3a').setAttribute("name", "campo3a");		
			
			switch (tipologia)
			{
			case "Voltura":

				document.getElementById('select_pod').style.display = "none";
				document.getElementsByName("pod")[0].value = "";
				switch_editor(1);
				document.getElementById('commento').checked = true;
				break;	
				
			case "Nuova Fornitura":

				document.getElementById('select_pod').style.display = "none";
				document.getElementsByName("pod")[0].value = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Indirizzo/Localita'/CAP";
				cell1.id = "Indirizzo/Localita'/CAP";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("input");
			
				element1.type = "text";
				element1.name = "campo1";
				element1.id = "campo1";
				element1.value = "";
				element1.className = "required";
				element1.size = "40";
				element1.maxLength = "124";
				cell2.appendChild(element1);

				rowCount = rowCount + 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo2";
				row.className = "rigaclass";
 
				var cell3 = row.insertCell(0);
				cell3.innerHTML  = "Potenza [kW]";
				cell3.id = "Potenza [kW]";
 
				var cell4 = row.insertCell(1);
				
				var element2 = document.createElement("input");
			
				element2.type = "text";
				element2.name = "campo2";
				element2.id = "campo2";
				element2.value = "";
				element2.className = "required";
				element2.size = "40";
				element2.maxLength = "124";
				cell4.appendChild(element2);	
				
				rowCount = rowCount + 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo3";
				row.className = "rigaclass";
 
				var cell5 = row.insertCell(0);
				cell5.innerHTML  = "Tensione [V]";
				cell5.id = "Tensione [V]";
 
				var cell6 = row.insertCell(1);
				
				var element3 = document.createElement("input");
			
				element3.type = "text";
				element3.name = "campo3";
				element3.id = "campo3";
				element3.value = "";
				element3.className = "required";
				element3.size = "40";
				element3.maxLength = "124";
				cell6.appendChild(element3);	
				
				break;

			case "Spostamento":

				document.getElementById('select_pod').style.display = "";
				document.getElementsByName("pod")[0].value = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Oltre 10 metri";
				cell1.id = "Oltre 10 metri";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("input");
			
				element1.type = "checkbox";
				element1.name = "campo1";
				element1.id = "campo1";
				element1.value = "Si";
				element1.className = "required";
				cell2.appendChild(element1);
				break;				

			case "Attivazione":

				document.getElementById('select_pod').style.display = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 2;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Definitiva/Temporanea";
				cell1.id = "Definitiva/Temporanea";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("select");
			
				element1.name = "campo1";
				element1.id = "campo1";
				element1.className = "required";
				cell2.appendChild(element1);
				
				var option0 = document.createElement('option');
				var t = document.createTextNode("Selezionare ...");
						option0.setAttribute("value", "NA");
						option0.appendChild(t);
						element1.appendChild(option0);
				
						var option1 = document.createElement('option');
						var t = document.createTextNode("Definitiva");
						option1.appendChild(t);
						element1.appendChild(option1);

						var option2 = document.createElement('option');
						var t = document.createTextNode("Temporanea");
						option2.appendChild(t);
						element1.appendChild(option2);
						
						element1.onchange = function (){
							 rowCount = rowCount + 1 - $('.rigaclass').length;
							 $('.rigaclass').slice(1).remove();
							/*	if ( document.getElementsByClassName("rigaclass").length > 1 ) 
								{
									
									var rigaclasses = document.getElementsByClassName("rigaclass");
									var lungh = rigaclasses.length;

									while  ( rigaclasses.length > 1 ) 
									{
										var lunghe = rigaclasses.length - 1;
										rigaclasses[lunghe].parentNode.removeChild(rigaclasses[lunghe]);
										rowCount = rowCount -1;
									}	
								}	*/
							if (option2.selected == true) {
								document.getElementById('tempo').style.display = "";
								document.getElementById('campo2a').setAttribute("name", "campo2");
								document.getElementById('campo3a').setAttribute("name", "campo3");
				
							}
							else {
								document.getElementById('tempo').style.display = "none";								
								document.getElementById('campo2a').setAttribute("name", "campo2a");
								document.getElementById('campo3a').setAttribute("name", "campo3a");	
								
							}
							}	
				break;				
				
			case "Disattivazione":

				document.getElementById('select_pod').style.display = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 2;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Definitiva/Temporanea";
				cell1.id = "Definitiva/Temporanea";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("select");
			
				element1.name = "campo1";
				element1.id = "campo1";
				element1.className = "required";
				cell2.appendChild(element1);
				
				var option0 = document.createElement('option');
				var t = document.createTextNode("Selezionare ...");
						option0.setAttribute("value", "NA");
						option0.appendChild(t);
						element1.appendChild(option0);
				
						var option1 = document.createElement('option');
						var t = document.createTextNode("Definitiva");
						option1.appendChild(t);
						element1.appendChild(option1);

						var option2 = document.createElement('option');
						var t = document.createTextNode("Temporanea");
						option2.appendChild(t);
						element1.appendChild(option2);
						
						element1.onchange = function (){
							 rowCount = rowCount + 1 - $('.rigaclass').length;
							 $('.rigaclass').slice(1).remove();
							 
					/*			if ( document.getElementsByClassName("rigaclass").length > 1 ) 
								{
									
									var rigaclasses = document.getElementsByClassName("rigaclass");
									var lungh = rigaclasses.length;

									while  ( rigaclasses.length > 1 ) 
									{
										var lunghe = rigaclasses.length - 1;
										rigaclasses[lunghe].parentNode.removeChild(rigaclasses[lunghe]);
										rowCount = rowCount -1;
									}	
								}	*/
							if (option2.selected == true) {
								document.getElementById('tempo').style.display = "";
								document.getElementById('campo2a').setAttribute("name", "campo2");
								document.getElementById('campo3a').setAttribute("name", "campo3");
				
							}
							else {
								document.getElementById('tempo').style.display = "none";								
								document.getElementById('campo2a').setAttribute("name", "campo2a");
								document.getElementById('campo3a').setAttribute("name", "campo3a");	
								
								rowCount = rowCount + 1;
								var row = table.insertRow(rowCount);
								row.id = "rigatipo2";
								row.className = "rigaclass";
				
								var cell3 = row.insertCell(0);
								cell3.innerHTML  = "Rimozione contatore";
								cell3.id = "Rimozione contatore";
				
								var cell4 = row.insertCell(1);
								
								var element2 = document.createElement("input");
							
								element2.type = "checkbox";
								element2.name = "campo2";
								element2.id = "campo2";
								element2.value = "Si";
								element2.className = "required";
								cell4.appendChild(element2);								

								rowCount = rowCount + 1;
								var row = table.insertRow(rowCount);
								row.id = "rigatipo3";
								row.className = "rigaclass";
				
								var cell5 = row.insertCell(0);
								cell5.innerHTML  = "Rimozione presa";
								cell5.id = "Rimozione presa";
				
								var cell6 = row.insertCell(1);
								
								var element3 = document.createElement("input");
							
								element3.type = "checkbox";
								element3.name = "campo3";
								element3.id = "campo3";
								element3.value = "Si";
								element3.className = "required";
								cell6.appendChild(element3);
							}
							}	
				break;				

			case "Variazione Tensione":

				document.getElementById('select_pod').style.display = "";
				document.getElementsByName("pod")[0].value = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Tensione attuale [V]";
				cell1.id = "Tensione attuale [V]";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("input");
			
				element1.type = "text";
				element1.name = "tensione";
				element1.id = "tensione";
				element1.setAttribute("readOnly","true")
				element1.value = "";
				element1.className = "required";
				element1.size = "1";
				row.style.display = "none";
				cell2.appendChild(element1);
				

				rowCount = rowCount + 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo2";
				row.className = "rigaclass";
 
				var cell3 = row.insertCell(0);
				cell3.innerHTML  = "Tensione richiesta [V]";
				cell3.id = "Tensione richiesta [V]";
 
				var cell4 = row.insertCell(1);
				
				var element2 = document.createElement("input");
			
				element2.type = "text";
				element2.name = "campo1";
				element2.id = "campo1";
				element2.value = "";
				element2.className = "required";
				element2.size = "40";
				element2.maxLength = "124";
				cell4.appendChild(element2);					
				break;					
				
			case "Variazione Potenza":

				document.getElementById('select_pod').style.display = "";
				document.getElementsByName("pod")[0].value = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Potenza attuale [V]";
				cell1.id = "Potenza attuale [V]";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("input");
			
				element1.type = "text";
				element1.name = "potenza";
				element1.id = "potenza";
				element1.setAttribute("readOnly","true")
				element1.value = "";
				element1.className = "required";
				element1.size = "5";
				row.style.display = "none";
				cell2.appendChild(element1);
				

				rowCount = rowCount + 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo2";
				row.className = "rigaclass";
 
				var cell3 = row.insertCell(0);
				cell3.innerHTML  = "Potenza richiesta [V]";
				cell3.id = "Potenza richiesta [V]";
 
				var cell4 = row.insertCell(1);
				
				var element2 = document.createElement("input");
			
				element2.type = "text";
				element2.name = "campo1";
				element2.id = "campo1";
				element2.value = "";
				element2.className = "required";
				element2.size = "40";
				element2.maxLength = "124";
				cell4.appendChild(element2);					
				break;					

			case "Variazione Tipologia d'uso":

				document.getElementById('select_pod').style.display = "";
				document.getElementsByName("pod")[0].value = "";
				document.getElementById('commento').checked = false;
				switch_editor(0);
				
				var table = document.getElementById("tableid");
 
				var rowCount = table.rows.length - 1;
				var row = table.insertRow(rowCount);
				row.id = "rigatipo";
				row.className = "rigaclass";
 
				var cell1 = row.insertCell(0);
				cell1.innerHTML  = "Tipologia d'uso";
				cell1.id = "Tipologia d'uso";
 
				var cell2 = row.insertCell(1);
				
				var element1 = document.createElement("select");
			
				element1.name = "campo1";
				element1.id = "campo1";
				element1.className = "required";
				cell2.appendChild(element1);
				
				var option0 = document.createElement('option');
				var t = document.createTextNode("Pubblica Illuminazione");
						option0.setAttribute("value", "Pubblica Illuminazione");
						option0.appendChild(t);
						element1.appendChild(option0);
				
						var option1 = document.createElement('option');
						var t = document.createTextNode("Altri Usi");
						option1.appendChild(t);
						element1.appendChild(option1);
				
						var option2 = document.createElement('option');
						var t = document.createTextNode("Domestico");
						option2.appendChild(t);
						element1.appendChild(option2);				
				break;					

			case "Altro":

				document.getElementById('select_pod').style.display = "none";
				document.getElementsByName("pod")[0].value = "";
				switch_editor(1);
				document.getElementById('commento').checked = true;
				break;
				
			}

/*			

                    <table style="" id="typo_table1" class="admintable">
					<tr>
						<td class="key" width="150"><?php echo PFformat::Lang('POD');?></td>
						<td><?php echo $form->SelectPod('pod', -1);?></td>
					</tr>
					<tr>
						<td class="key" width="150"><?php echo "Dati Fiscali";?></td>
						<td><?php echo $form->InputField('campo1*', '', 'size="40" maxlength="124"');?></td>		
					</tr>						
					</table>	
			
myBody = document.getElementsByTagName("body")[0];
myBodyElements = myBody.getElementsByTagName("tr");	
//myP = myBodyElements[1];
myNewPTAGnode = document.createElement("tr");
//myBody.appendChild(myNewPTAGnode);

myTextNode = document.createTextNode("world");
myNewPTAGnode.appendChild(myTextNode);
*/
}

function add_user()
{
	var template = document.getElementById('user_template').innerHTML;
	var dest     = document.getElementById('user_container');

	var div = document.createElement('div');
	    div.style.padding = '2px';
	    div.innerHTML = template;

	dest.appendChild(div);
}
</script>
<?php echo $form->start();?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('TASKS');?> :: <?php echo PFformat::Lang('NEW');?></h1>
    </div>
    <div class="pf_body">
    
        <!-- NAVIGATION START-->
        <?php PFpanel::Position('tasks_nav');?>
        <!-- NAVIGATION END -->
<?php

$core = PFcore::GetInstance();

$user    = PFuser::GetInstance();
$id = (int) JRequest::GetVar('id');


$project = $user->GetWorkspace();
			if(!$project) {
				$project = 0;

				$display_all = true;
			}
			
$db   = PFdatabase::GetInstance();
$cfg  = PFconfig::GetInstance();
					$query = "SELECT title FROM #__pf_projects WHERE id = '$project'";

                    $db->setQuery($query);
					
                    $ptitle = $db->loadResult();
					
jimport('joomla.html.pane');
$tabs = JPane::getInstance('Tabs');
echo $tabs->startPane('paneID');
echo $tabs->startPanel(PFformat::Lang('GENERAL_INFORMATION'), 'pane1');
?>
                
                <table id="tableid" class="admintable">
                    <tr>
                        <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
                        <td><?php echo $form->InputField('title*', '', 'size="40" maxlength="124"');?></td>
                    </tr>
                    <?php if($use_milestones) { ?>
                    <tr>
                        <td class="key"><?php echo PFformat::Lang('MILESTONE');?></td>
                        <td><?php echo $form->SelectMilestone('milestone', -1);?></td>
                    </tr>
                    <?php } if($use_progperc) { ?>
                    <tr>
                        <td class="key"><?php echo PFformat::Lang('PROGRESS');?></td>
                        <td><?php echo $form->SelectProgress('progress');?></td>
                    </tr>
                    <?php } else { ?>
                    <tr>
                        <td class="key"><?php echo PFformat::Lang('TASK_COMPLETED');?></td>
                        <td><?php echo $form->SelectNY('progress', 0);?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="key"><?php echo PFformat::Lang('PRIORITY');?></td>
                        <td><?php echo $form->SelectPriority('prio', -1);?></td>
                    </tr>
                    <tr>
                        <td class="key"><?php echo PFformat::Lang('DEADLINE');?></td>
                        <td><input type="checkbox" name="has_deadline" value="1" onclick="switch_deadline(this.checked);"/>
                        <?php echo PFformat::Lang('TASK_HAS_DEADLINE'); ?></td>
                    </tr>
                    <tr style="display:none" id="dealine_table">
                        <td class="key"><?php echo PFformat::Lang('DATE');?></td>
                        <td>
                            <?php 
							if ($now == $date_format) {
								echo JHTML::calendar(JHTML::_('date', '', PFformat::JhtmlCalendarDateFormat()), 'edate', 'edate');
							}
							else {
								echo JHTML::calendar($now, 'edate', 'edate', $date_format);
							}
							?>
                            <?php echo PFformat::Lang('HOUR');?>
                            <?php echo $form->SelectHour('hour', $hour);?>
                            <?php echo PFformat::Lang('MINUTE');?>
                            <?php echo $form->SelectMinute('minute', $minute);?>
                            <?php echo $form->SelectAmPm('ampm', $ampm);?>
                        </td>
                    </tr>
			<!-- 		<tr><td class="key required" width="150"><?php echo PFformat::Lang('POD');?></td>
						<td><?php echo $form->InputField('pod*', '', 'size="40" maxlength="150"');?></td> 
					</tr> -->
					<!--  select typology to display different fields-->
                    <tr>
						<td class="key"><?php echo PFformat::Lang('TYPOLOGY');?></td>
						<td><?php echo $form->SelectTypology('typology', -1, 'id="typolo" onchange="switch_typo();"');?></td>
                    </tr>	
                    <tr>
                        <td class="key">Commento</td>
                        <td><input id="commento" type="checkbox" name="editor" value="1" onclick="switch_editor(this.checked);"/>
                        Inserire informazioni aggiuntive</td>
                    </tr>						
					
				<!--	<tr style="display:none" id="select_pod">
						<td class="key" width="150"><?php echo PFformat::Lang('POD');?></td>
						<td><?php echo $form->SelectPod('pod', -1);?></td>
					</tr>		-->			
					<tr style="display:none" id="select_pod"><td class="key required" width="150"><?php echo PFformat::Lang('POD');?></td>
						<td><?php echo $form->InputField('pod*', '', 'size="40" maxlength="150" id="ricerca"');?>&nbsp&nbspRicerca <button onclick="choose('ricerca', '<?php echo $ptitle ?>','pod');return false;">&nbsp&nbsp&nbspPOD&nbsp&nbsp&nbsp</button><button onclick="choose('ricerca', '<?php echo $ptitle ?>','CodiceCliente');return false;">&nbspEnelTel&nbsp</button></td>
					</tr>	
					<!-- EOM -->

                        <tr style="display:none" id="tempo" ><td>Da</td><td>
                            <?php 
								echo JHTML::calendar(JHTML::_('date', '', PFformat::JhtmlCalendarDateFormat()), 'campo2a', 'campo2a');
							?>
                        <span style="padding: 15px 15px">A</span>
                            <?php 
								echo JHTML::calendar(JHTML::_('date', '', PFformat::JhtmlCalendarDateFormat()), 'campo3a', 'campo3a');
							?>
                        </td>
						</tr>
					
                    <tr>
                        <td colspan="2" style="display:none"  id="editor">
                        <?php 
                        if($use_editor && !defined('PF_DEMO_MODE')) { 
              	            echo $editor->display('text',  "" , '100%', '350', '75', '20') ;
                        }
                        else {
                 	        echo $form->TextArea('text','','75', '20');
                        }
                        ?>
                        </td>
                    </tr>
                </table>
            
<?php
echo $tabs->endPanel();
echo $tabs->startPanel(PFformat::Lang('TASK_RESPONSIBLE'), 'pane2');
?>
                <table class="admintable">
                    <tr>
                        <td class="key" width="150" valign="top"><a href="javascript:add_user();"><?php echo PFformat::Lang('ADD_MEMBER');?></a></td>
                        <td id="user_container"></td>
                    </tr>
                </table>
<?php
echo $tabs->endPanel();
echo $tabs->startPanel("Allegato", 'pane3');
?>

        <div class="col">
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
                <table class="admintable">
                    <tr>
                        <td class="key required" width="150"><?php echo PFformat::Lang('FILE');?></td>
                        <td><?php echo $form->FileField('file[]*', '', 'size="40"');?></td>
                    </tr>
                    <tr>
                        <td class="key" width="150" valign="top"><?php echo PFformat::Lang('DESC');?></td>
                        <td><?php echo $form->InputField('description[]', '', 'size="80" maxlength="124"');?></td>
                    </tr>
                </table>
            </fieldset>
            <div id="file_container"></div>
        </div>
        <div class="clr"></div>
<?php
echo $tabs->endPanel();
echo $tabs->endPane();
?>
    </div>
</div>
<?php
echo $form->HiddenField("option");
echo $form->HiddenField("section");
echo $form->HiddenField("task");
echo $form->HiddenField("limitstart");
echo $form->HiddenField("keyword");
echo $form->HiddenField("apply", 0);
echo $form->HiddenField("dir");
echo $form->End();
?>
<div id="user_template" style="display:none">
<?php echo $select_user; ?>
</div>