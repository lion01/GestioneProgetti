<?php
/**
* $Id: form_edit.php 837 2010-11-17 12:03:35Z eaxs $
* @package   Databeis
* @copyright Copyright (C) 2006-2010 DataBeis. All rights reserved.
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
        <h1><?php echo $ws_title." / ".PFformat::Lang('GROUPS');?> :: <?php echo PFformat::Lang('EDIT_GROUP');?></h1>
    </div>
    <div class="pf_body">
    
    <!-- NAVIGATION START -->
    <?php PFpanel::Position('groups_nav');?>
    <!-- NAVIGATION END -->
    
    <!-- START LEFT COL -->
    <div class="col">
    
        <fieldset class="adminform">
            <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
            <table class="admintable">
                <tr>
                    <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
                    <td>
                        <?php
                        if(in_array($row->id, $restricted)) {
                            if($jversion->RELEASE == '1.6') {
                                echo str_replace('{group_name}', $row->jtitle, PFformat::Lang($row->title));
                            } 
                            else {
                                echo PFformat::Lang($row->title);
                            }
                        }
                        else {
                            echo $form->InputField('title*', $row->title, 'size="35"');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="key" width="150"><?php echo PFformat::Lang('DESC');?></td>
                    <td>
                        <?php
                        if(in_array($row->id, $restricted)) {
                            echo PFformat::Lang($row->description);
                        }
                        else {
                            echo $form->InputField('description', $row->description, 'size="50"');
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
         
        <fieldset class="adminform">
            <legend><?php echo PFformat::Lang('PERMISSIONS');?></legend>
            <?php echo PFgroupsClass::PermissionTable($row->permissions);?>
        </fieldset>
         
    </div>
    <!-- END LEFT COL -->
    
    <!-- START RIGHT COL -->
    <div class="col">
    
         <fieldset class="adminform">
             <legend><?php echo PFformat::Lang('GROUP_MEMBERS');?></legend>
             <?php if(in_array($row->id, $restricted)) { ?>
             <table class="admintable">
                 <tr>
                     <td class="key" width="150" valign="top"><?php echo PFformat::Lang('ADD_MEMBER');?></td>
                     <td><?php echo PFformat::Lang('GROUP_AUTO_MEMBERS'); ?></td>
                 </tr>
             </table>
             <?php
             }
             else {
             ?>
             <table class="admintable">
                 <tr>
                     <td class="key" width="150" valign="top">
                         <a href="javascript:addUserSelect('user_template', 'member_list');"><?php echo PFformat::Lang('ADD_MEMBER');?></a>
                     </td>
                     <td id="member_list">
                     <?php
                         if(!is_array($row->users)) $row->users = array();
                  
                         foreach ($row->users AS $user)
                         {
                  	         echo "<div style='padding-bottom:2px;'>".$form->SelectUser('user_id[]', $user)."</div>";
                         }
                     ?>
                     </td>
                 </tr>
             </table>
             <?php } ?>
         </fieldset>
          
    </div>
    <!-- END RIGHT COL -->
    
    <div class="clr"></div>

    </div>
</div>
<?php
echo $form->HiddenField('option', JRequest::getVar('option'));
echo $form->HiddenField('section', JRequest::getVar('section'));
echo $form->HiddenField('task');
echo $form->HiddenField('id');

if(in_array($row->id, $restricted)) {
    echo $form->HiddenField('title');
}
if(in_array($row->id, $restricted)) {
    echo $form->HiddenField('description');
}

echo $form->end();
?>

<div id="user_template" style="display:none">
   <?php echo $select_user; ?>
</div>

<script type="text/javascript">
function addUserSelect(template_id, target_id)
{
	var template = document.getElementById(template_id).innerHTML;
	
	var div = document.createElement('div');
	    div.style.padding = '2px';
	    div.innerHTML = template;

	document.getElementById(target_id).appendChild(div);
}
function task_update()
{
	var d = document.adminForm;
	var e = "";
	if(d.title.value == '') {e = "<?php echo PFformat::Lang('V_TITLE');?>";}
	if(e) {alert(e);}
	else {submitbutton('task_update');}
}
</script>