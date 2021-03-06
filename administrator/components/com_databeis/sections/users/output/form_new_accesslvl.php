<?php
/**
* $Id: form_new_accesslvl.php 837 2010-11-17 12:03:35Z eaxs $
* @package    Databeis
* @subpackage Users
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
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('USERS');?> :: <?php echo PFformat::Lang('ACCESS_LVLS');?> :: <?php echo PFformat::Lang('NEW');?></h1>
    </div>
    <div class="pf_body">
    
        <!-- NAVIGATION START -->
        <?php PFpanel::Position('users_nav');?>
        <!-- NAVIGATION END -->
        
        <div class="col">
        
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
                <table class="admintable">
                    <tr>
                        <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
                        <td><?php echo $form->InputField('title*', '', 'size="60" maxlength="124"');?></td>
                    </tr>
                    <tr <?php if(!$use_score) { echo 'style="display:none !important;"'; } ?>>
                        <td class="key" width="150"><?php echo PFformat::Lang('SCORE');?></td>
                        <td><?php echo $form->InputField('score', '0', 'size="10" maxlength="11"');?></td>
                    </tr>
                </table>
            </fieldset>
            
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('FLAG');?></legend>
                <table class="admintable">
                    <tr>
                       <td class="key" width="150"><?php echo PFformat::Lang('PFL_NONE');?></td>
                       <td><input type="radio" name="f" value="" checked="checked"/></td>
                    </tr>
                    <?php
                    foreach ($flags AS $f)
                    {
               	        if($f->name == 'project_administrator' && $flag == '') continue;
               	        if($f->name == 'system_administrator' && $flag == '') continue;
               	        if($f->name == 'system_administrator' && $flag == 'project_administrator') continue;
               	    ?>
               	    <tr>
               	        <td class="key" width="150"><?php echo PFformat::Lang($f->title);?></td>
               	        <td><input type="radio" name="f" value="<?php echo $f->name;?>"/></td>
               	    </tr>
               	    <?php } ?>
                </table>
            </fieldset>
                 
        </div>
        <div class="col">
        
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('USERS');?></legend>
                <table class="admintable">
                    <tr>
                       <td class="key" width="150" valign="top"><a href="javascript:addUserSelect('user_template', 'member_list');"><?php echo PFformat::Lang('ADD_MEMBER');?></a></td>
                       <td id="member_list"></td>
                    </tr>
                </table>
            </fieldset>
                
       </div>
       <div class="clr"></div>

    </div>
</div>
<?php
echo $form->HiddenField("option", "com_databeis");
echo $form->HiddenField("section", "users");
echo $form->HiddenField("task", "task_save_accesslvl");
echo $form->End();
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
function task_save_accesslvl()
{
	var d = document.adminForm;
	var e = "";
	if(d.title.value == '') {e = "<?php echo PFformat::Lang('V_TITLE');?>";}
	if(e) {alert(e);}
	else {submitbutton('task_save_accesslvl');}
}
</script>