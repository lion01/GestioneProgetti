<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

echo $form->start();
?>

<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('NEW_FILE');?></h1>
    </div>
    <div class="pf_body">

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
        
        <?php if($attach) { ?>
            <div class="col">
                <fieldset class="adminform">
                    <legend><?php echo PFformat::Lang('ATTACH_TO_TASKS');?></legend>
                    <table class="admintable">
                        <tbody>
                            <tr>
                                <td class="key" width="150" valign="top">
                                    <a href="javascript:add_task()"><?php echo PFformat::Lang('ADD_TASK');?></a>
                                </td>
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
echo $form->HiddenField("task", "task_save_file");
echo $form->HiddenField("dir");
echo $form->End();
?>
<div id="task_list" style="display:none">
    <?php echo $form->SelectTask('tasks[]');?>
</div>
<div id="add_file" style="display:none">
    <fieldset class="adminform">
        <legend><?php echo PFformat::Lang('GENERAL_INFORMATION');?></legend>
        <table class="admintable">
            <tr>
                <td class="key required" width="150"><?php echo PFformat::Lang('FILE');?></td>
                <td><?php echo $form->FileField('file[]*', '', 'size="40"');?></td>
            </tr>
            <tr>
                <td class="key" width="150" valign="top"><?php echo PFformat::Lang('DESC');?></td>
                <td><?php echo $form->InputField('description[]', '', 'size="80" maxlength="128"');?></td>
            </tr>
        </table>
    </fieldset>
</div>
<script type="text/javascript">
function task_save_file()
{
	submitbutton('task_save_file');
}
function addFile(template_id, target_id)
{
	var template = document.getElementById(template_id).innerHTML;
	
	var div = document.createElement('div');
	    div.style.padding = '2px';
	    div.innerHTML = template;

	document.getElementById(target_id).appendChild(div);
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