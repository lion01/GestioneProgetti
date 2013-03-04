<?php
/**
* $Id: form_new_folder.php 837 2010-11-17 12:03:35Z eaxs $
* @package    Databeis
* @subpackage Filemanager
* @copyright  Copyright (C) 2006-2010 DataBeis. All rights reserved.
* @license    http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.php
*
* This file is part of Databeis.
*
* Databeis is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License License as published by
* the Free Software Foundation, either version 3 of the License,
* or any later version.
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

echo $form->Start();
?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('NEW_FOLDER');?></h1>
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
                        <td><?php echo $form->InputField('title*', '', 'size="40" maxlength="56"');?></td>
                    </tr>
                    <tr>
                        <td class="key" width="150"><?php echo PFformat::Lang('DESC');?></td>
                        <td><?php echo $form->InputField('description', '', 'size="60" maxlength="128"');?></td>
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
                            <td id="attachments" valign="top"></td>
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
echo $form->HiddenField("option");
echo $form->HiddenField("section");
echo $form->HiddenField("task", "task_save_folder");
echo $form->HiddenField("dir");
echo $form->End();
?>
<div id="task_list" style="display:none">
    <?php echo $form->SelectTask('tasks[]');?>
</div>
<script type="text/javascript">
function task_save_folder()
{
	var d = document.adminForm;
	var e = "";
	if(d.title.value == '') {e = "<?php echo PFformat::Lang('V_TITLE');?>";}
	if(e) {alert(e);}
	else { submitbutton('task_save_folder');}
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