<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<style type="text/css">
.new_line {
    color:<?php echo $new_line_color;?>;
    background-color:<?php echo $new_line_bg;?>;
    display:block;
}
.missing_line {
    color:<?php echo $missing_line_color;?>;
    background-color:<?php echo $missing_line_bg;?>;
    display:block;
}
.line_id
{
    background-color:#666666;
    color:white;
    border-right:1px solid black;
    width:35px;
    border-bottom:1px solid #cccccc;
    font-family:Courier, inherit;
}
.line_content
{
    padding-left:5px;
    font-family:Courier, inherit;
}
.compare_container
{
    height:300px;
    overflow:auto;
}
</style>
<?php echo $form->Start();?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('COMPARE_VERSIONS');?></h1>
    </div>
    <div class="pf_body">
    
        <table width="100%">
            <tr>
                <td style="border-bottom:2px solid #cccccc;padding-bottom:3px;">
                    <?php echo PFfilemanagerHelper::RenderSelectNoteVersion("n1", $id, $row1->id);?>
                    <?php echo "<strong>".PFformat::Lang('TOTAL_LINES').":</strong> ".count($content1);?>
                    <?php echo " :: <strong>".PFformat::Lang('MISSING_LINES').":</strong> ".count($missing);?>
                </td>
                <td style="border-bottom:2px solid #cccccc;padding-bottom:3px;">
                    <?php echo PFfilemanagerHelper::RenderSelectNoteVersion("n2", $id, $row2->id);?>
                    <?php echo "<strong>".PFformat::Lang('TOTAL_LINES').":</strong> ".count($content2);?>
                    <?php echo " :: <strong>".PFformat::Lang('NEW_LINES').":</strong> ".count($new);?>
                </td>
            </tr>
            <tr>
                <td valign="top" width="50%" style="border-right:2px solid #cccccc">
                    <div class="compare_container">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <?php
                            foreach($content1 AS $number1 => $line1)
                            {
                                $s1 = "";
                                $s2 = "";
                                if(in_array($number1, $missing)) {
                                    $s1 = "<span class='missing_line'>";
                                    $s2 = "</span>";
                                }
                            ?>
                            <tr>
                                <td class="line_id"><?php echo $number1+1;?></td>
                                <td class="line_content"><?php echo $s1.$line1.$s2;?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </td>
                <td valign="top" width="50%" style="overflow:auto">
                    <div class="compare_container">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <?php
                            foreach($content2 AS $number2 => $line2)
                            {
                                $s1 = "";
                                $s2 = "";
                                
                                if(in_array($number2, $new)) {
                                    $s1 = "<span class='new_line'>";
                                    $s2 = "</span>";
                                }
                            ?>
                            <tr>
                                <td class="line_id"><?php echo $number2+1;?></td>
                                <td class="line_content"><?php echo $s1.$line2.$s2;?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        
    </div>
</div>
<?php
echo $form->HiddenField("option");
echo $form->HiddenField("section");
echo $form->HiddenField("task");
echo $form->HiddenField("dir");
echo $form->HiddenField("id");
echo $form->End();
?>