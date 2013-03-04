<?php
/**
* $Id: form_edit_note.php 837 2010-11-17 12:03:35Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2009 DataBeis. All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License,
* or any later version.
*
* Databeis is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public License
* along with Databeis.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

echo $form->Start();
?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('EDIT');?></h1>
    </div>
    <div class="pf_body">
    
        <!-- NAVIGATION START-->
        <?php PFpanel::Position('filemanager_nav');?>
        <!-- NAVIGATION END -->
        
        <div class="col">
        
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
                <table class="admintable">
                    <tr>
                        <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
                        <td width="100%"><?php echo $form->InputField('title*', '', 'size="40" maxlength="56"');?></td>
                    </tr>
                    <tr>
                        <td class="key" width="150"><?php echo PFformat::Lang('DESC');?></td>
                        <td><?php echo $form->InputField('description', '', 'size="60" maxlength="124"');?></td>
                    </tr>
                    <tr>
                        <td class="key" width="150" valign="top"><?php echo PFformat::Lang('CONTENT');?></td>
                        <td>
                        <?php 
                        if($use_editor && !defined('PF_DEMO_MODE')) { 
                     	    echo $editor->display( 'text',  $row->content , '100%', '350', '75', '20' ) ;
                        } 
                        else {
                     	    echo $form->TextArea('text',$row->content,'75', '20');
                        }
                        ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
        </div>
        <?php if($attach) { ?>
        <div class="col">       
           
		    <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('ATTACH_TO_TASKS');?></legend>
                <table class="admintable">
                    <tbody>
                        <tr>
                            <td class="key" width="150" valign="top"><a href="javascript:add_task()"><?php echo PFformat::Lang('ADD_TASK');?></a></td>
                            <td id="attachments" valign="top">
                            <?php 
                            foreach ($row->attachments AS $tid)
                            {
                  	            echo "<div style='padding:2px;'>".$form->SelectTask('tasks[]', $tid)."</div>";
                            }
                            ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
		    </fieldset>
		    
        </div>         
        <?php } ?>
        <div class="clr"></div>
           
    </div>
</div>
<?php
$form->SetBind(true, "REQUEST");
echo $form->HiddenField("option");
echo $form->HiddenField("section");
echo $form->HiddenField("task", "task_update_note");
echo $form->HiddenField("dir");
echo $form->HiddenField("id");
echo $form->End();
?>
<div id="task_list" style="display:none">
<?php echo $form->SelectTask('tasks[]');?>
</div>
<script type="text/javascript">
function task_update_note()
{
	var d = document.adminForm;
	var e = "";
	if(d.title.value == '') {e = "<?php echo PFformat::Lang('V_TITLE');?>";}
	if(e) {alert(e);}
	else { <?php if($use_editor) { echo $editor->save( 'text' ); } ?> submitbutton('task_update_note');}
}
function add_task()
{
	var template = document.getElementById('task_list').innerHTML;
	var dest     = document.getElementById('attachments');
	
	var div = document.createElement('div');
	    div.style.padding = '2px';
	    div.innerHTML = template;
	    
	dest.appendChild(div);
}
</script>
