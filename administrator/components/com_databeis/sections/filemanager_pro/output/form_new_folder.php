<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php echo $form->Start();?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('NEW_FOLDER');?></h1>
    </div>
    <div class="pf_body">
    
        <div class="col">
        
            <fieldset class="adminform">
                <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
                <table class="admintable">
                    <tr>
                        <td class="key required" width="150"><?php echo PFformat::Lang('TITLE');?></td>
                        <td><?php echo $form->InputField('title*', '', 'size="30" maxlength="56"');?></td>
                    </tr>
                    <tr>
                        <td class="key" width="150"><?php echo PFformat::Lang('DESC');?></td>
                        <td><?php echo $form->InputField('description', '', 'size="40" maxlength="128"');?></td>
                    </tr>
                </table>
            </fieldset>
            
        </div>
        
        <div class="col">
                
            <?php if($restrict) { ?>
                <fieldset class="adminform">
                    <legend><?php echo PFformat::Lang('RESTRICT_FOLDER_ACCESS');?></legend>
                    <table class="admintable">
                        <tbody>
                            <tr>
                                <td class="key" width="150" valign="top">
                                    <p style="font-weight:normal;text-align:left"><?php echo PFformat::Lang('ADD_GROUP_DESC');?></p>
                                </td>
                                <td id="access_groups" valign="top">
                                    <div style="padding-bottom:10px;">
                                        <a href="javascript:add_group()" class="pf_button"><?php echo PFformat::Lang('ADD_GROUP');?></a>
                                    </div>
                                    <?php 
                                    if(count($parent_groups)) { ?>
                                        <strong><?php echo PFformat::Lang('RESTRICTED_BY_PARENT');?></strong>
                                        <?php 
                                        foreach($parent_groups AS $g)
                                        {
                                            echo "<div style='padding:2px;'>".PFfilemanagerHelper::SelectNewAccessGroup('groups_disabled[]', $g, true)."</div>";
                                        }
                                        echo '<strong>'.PFformat::Lang('NEW_GROUPS').'</strong>';
                                    } 
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php } ?>
            
            <?php if($attach) { ?>
                <fieldset class="adminform">
                    <legend><?php echo PFformat::Lang('ATTACH_TO_TASKS');?></legend>
                    <table class="admintable">
                        <tbody>
                            <tr>
                                <td class="key" width="150" valign="top">
                                    <p style="font-weight:normal;text-align:left"><?php echo PFformat::Lang('ADD_TASK_DESC');?></p>
                                </td>
                                <td id="attachments" valign="top">
                                    <div style="padding-bottom:10px;">
                                        <a href="javascript:add_task()" class="pf_button"><?php echo PFformat::Lang('ADD_TASK');?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php } ?>
        
        </div>
                    
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
<div id="group_list" style="display:none">
<?php echo PFfilemanagerHelper::SelectNewAccessGroup('groups[]', 0, false);?>
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
function add_group()
{
	var template = document.getElementById('group_list').innerHTML;
	var dest     = document.getElementById('access_groups');
	
	var div = document.createElement('div');
	    div.style.padding = '2px';
	    div.innerHTML = template;
	    
	dest.appendChild(div);
}
</script>