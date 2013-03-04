<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header">
        <h1><?php echo $ws_title." / ".PFformat::Lang('FILEMANAGER');?> :: <?php echo htmlspecialchars($row->title);?>
        <?php if($v) { echo " :: ".PFformat::Lang('PFL_VERSION')." ".$class->GetRealNoteId($v, $id); } ?></h1>
    </div>
    <div class="pf_body">

        <div class="col"><?php PFpanel::Position('note_details_left'); ?></div>
        <div class="col"><?php PFpanel::Position('note_details_right'); ?></div>
        <div class="clr"></div>
        <div><?php PFpanel::Position('note_details_bottom'); ?></div>
   
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