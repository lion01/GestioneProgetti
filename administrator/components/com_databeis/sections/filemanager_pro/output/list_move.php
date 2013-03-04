<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );


echo $form->Start();
?>
<a id="pf_top"></a>
<div class="pf_container">
    <div class="pf_header componentheading">
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo PFformat::Lang('MOVE');?></h1>
    </div>
    <div class="pf_body">

        <!-- TABLE START -->
        <table class="pf_table adminlist" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                   <th align="center" class="sectiontableheader title">#</th>
                   <th align="left" width="25%" class="sectiontableheader title"><?php echo $table->TH(0); // TITLE ?></th>
                   <th align="left" width="35%" class="sectiontableheader title"><?php echo $table->TH(1); // DESC ?></th>
                   <th align="left" width="15%" class="sectiontableheader title"><?php echo $table->TH(2); // CDATE ?></th>
                   <th align="left" width="25%" class="sectiontableheader title"><?php echo $table->TH(3); // AUTHOR ?></th>
                   <th align="left" nowrap="nowrap" class="sectiontableheader title idcol pf_id_header"><?php echo $table->TH(4);  // ID ?></th>
                </tr>
            </thead>
            <tbody id="table_body">
                <?php
                $k = 0;
                $x = 0;
                /* START LIST FOLDERS */
 	            foreach ($folders AS $i => $row)
 	            {	
 	                JFilterOutput::objectHTMLSafe($row);
 	  	            if(!$row->id) continue;
 	  	            if(in_array($row->id, $mfolders) && count($mfolders) != 0)  continue;
 	  	 
 	  	            $link_open = "javascript:list_move($row->id);"
 	            ?>
  	            <tr class="pf_row<?php echo $k;?>">
  	                <td><?php echo  $x+1; ?></td>
  	                <td><a href="<?php echo $link_open;?>" class="pf_fm_folder"><span><?php echo $row->title; ?></span></a></td>
  	                <td><?php echo $row->description; ?></td>
  	                <td><?php echo PFformat::ToDate($row->cdate); ?></td>
  	                <td><?php echo $row->name; ?></td>
  	                <td class="idcol pf_id_cell"><?php echo $row->id; ?></td>
  	            </tr>
  	            <?php
  	                $k = 1 - $k;
  	                $x++;
                }
                /* END LIST FOLDERS */
 	            ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <!-- TABLE END -->

    </div>
</div>
<?php
echo $form->HiddenField('option');
echo $form->HiddenField('section');
echo $form->HiddenField('task');
echo $form->HiddenField('dir');
echo $form->HiddenField('boxchecked', 0);
echo $form->HiddenField('ob', $ob);
echo $form->HiddenField('od', $od);
foreach ($mfolders AS $id)
{
	$id = (int) $id;
	
	echo $form->HiddenField('folder[]', $id);
}
foreach ($mfiles AS $id)
{
	$id = (int) $id;
	
	echo $form->HiddenField('file[]', $id);
}
foreach ($mnotes AS $id)
{
	$id = (int) $id;
	
	echo $form->HiddenField('note[]', $id);
}
echo $form->End();
?>
<script type="text/javascript">
function list_move(d)
{
	document.adminForm.dir.value = d;
	submitbutton('list_move');
}
</script>