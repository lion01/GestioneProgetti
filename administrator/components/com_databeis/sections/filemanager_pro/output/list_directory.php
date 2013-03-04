<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');

$j_uri    = & JFactory::getURI();
$base_url = $j_uri->base();
$base_url = str_replace('administrator/', '', $base_url);

echo $form->Start();
?>
<a id="pf_top"></a>
<div class="pf_container">

    <div class="pf_header componentheading">
        <h3><?php echo $ws_title." / ".PFformat::Lang('FILEMANAGER').PFformat::SectionEditButton();?></h3>
    </div>

    <div class="pf_body">

        <!-- TABLE START -->
        <?php if($use_tree) { ?>
        <div class="pf_tree_wrapper" style="width:<?php echo $tree_width;?>px;">
            <?php echo PFfilemanagerTree::Render(0,0,$workspace);?>
        </div>
        <?php } ?>

        <table class="pf_table adminlist fmpro-table" cellpadding="0" cellspacing="0" <?php if($use_tree) { ?>style="margin-left:<?php echo $tree_width;?>px;width:80%;"<?php } ?>>
            <thead>
                <tr>
                    <th align="center" class="sectiontableheader title pf_number_header">#</th>
                    <?php if($can_move || $can_delete) { ?>
                        <th align="center" class="sectiontableheader title pf_check_header">
                            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $total; ?>);" />
                        </th>
                    <?php } ?>
                    <th align="left" class="sectiontableheader title pf_title_header"><?php echo $table->TH(0); // TITLE ?></th>
                    <th align="left" nowrap="nowrap" class="sectiontableheader title pf_action_header"></th>
                    <?php if(!$desc_tt) { ?><th align="left" class="sectiontableheader title"><?php echo $table->TH(1); // DESC ?></th><?php } ?>
                    <th align="left" class="sectiontableheader title"><?php echo $table->TH(2); // CDATE ?></th>
                    <th align="left" class="sectiontableheader title"><?php echo $table->TH(3); // AUTHOR ?></th>
                    <th align="left" class="sectiontableheader title idcol pf_id_header"><?php echo $table->TH(4);  // ID ?></th>
                </tr>
            </thead>
            <tbody id="table_body">
                <?php
                    $html = '';
                    $k = 0;
                    $x = 0;

                    // Parent folder button
                    if($dir) {
                        $plink = PFformat::Link("section=filemanager_pro&dir=$parent_folder");

                        $html .= '<tr class="pf_row1"><td class="pf_number_cell"></td>';
                        if($can_move || $can_delete) $html .= '<td class="pf_check_cell"></td>';
                        $html .= '<td colspan="6" class="pf_directory_title item_title" align="left">
                            <a href="'.$plink.'" class="pf_fm_folder_up"><span>..</span></a>
                        </td></tr>';
                    }

                    /* Ajax Upload */
                    if($user->Access('form_new_file', 'filemanager_pro') && $quick_u && !defined('PF_DEMO_MODE')) {
                        $html .= '<tr class="row0 sectiontableentry1">
                            <td colspan="8" align="center" style="text-align:center">
                                <div id="file-uploader">
		                            <noscript><p>Please enable JavaScript to use file uploader.</p></noscript>
	                            </div>
                            </td>
                        </tr>';
                    }

                    /* START LIST FOLDERS */
                    if($workspace) {
                        foreach ($folders AS $i => $row)
     	                {
     	                    if(!$row->id) continue;
     	  	                JFilterOutput::objectHTMLSafe($row);

     	  	                // Check permission
 	  	                    $can_move2   = $user->Access('list_move', 'filemanager_pro', $row->author);
 	  	                    $can_delete2 = $user->Access('task_delete', 'filemanager_pro', $row->author);

     	  	                // Setup Tooltip
     	  	                $ht  = '';
     	  	                $tt  = '';
     	  	                $ast = '';
     	  	                if($desc_tt && trim($row->description) != '') {
                               $ht  = ' hasTip';
                               $tt  = ' title="::'.$row->description.'"';
                               $ast = ' *';
                            }

                            $icon = ($row->restricted) ? 'pf_fm_restricted' : 'pf_fm_folder';

     	  	                // Setup links
     	  	                $link_open = PFformat::Link("section=filemanager_pro&dir=$row->id");
     	  	                $link_edit = PFformat::Link("section=filemanager_pro&dir=$dir&task=form_edit_folder&id=$row->id");

     	  	                // Checkbox
     	  	                $checkbox = '<input id="cb'.$x.'" name="folder[]" value="'.$row->id.'" onclick="isChecked(this.checked);" type="checkbox"/>';

     	  	                // Create HTML output
     	  	                $html .= '<tr class="pf_row'.$k.'"><td class="pf_number_cell">'.($x+1).'</td>';

      	                    if($can_move || $can_delete) {
                                if($can_move2 || $can_delete2) {
                                    $html .= '<td align="center" class="pf_check_cell">'.$checkbox.'</td>';
                                }
                                else {
                                    $html .= '<td align="center" class="pf_check_cell"></td>';
                                }
                            }

                            $html .= '<td class="pf_directory_title item_title">
      	                    <a href="'.$link_open.'" class="'.$icon.$ht.'"'.$tt.'><span>'.$row->title.$ast.'</span></a></td>';

                            $html .= '<td class="pf_actions_cell">';
                            if($user->Access('form_edit_folder', 'filemanager_pro', $row->author)) {
                                $html .= $table->Menu();
      	                        $html .= $table->MenuItem($link_edit,'TT_EDIT','pf_edit');
      	                        $html .= $table->Menu(false);
      	                    }

      	                    $html .= '</td>';
      	                    if(!$desc_tt) '<td>'.$row->description.'</td>';

      	                    $html .= '<td>'.PFformat::ToDate($row->edate).'</td>
      	                        <td>'.$row->name.'</td>
      	                        <td class="idcol pf_id_cell">'.$row->id.'</td>
      	                    </tr>';
      	                    $k = 1 - $k;
      	                    $x++;
     	                }
                    }
                    else {
                        // Global view
                        foreach ($folders AS $i => $row)
     	                {
     	                    if(!$row->id) continue;
     	  	                JFilterOutput::objectHTMLSafe($row);

     	  	                // Setup links
     	  	                $link_open = PFformat::Link("section=filemanager_pro&workspace=$row->id&dir=0");

     	  	                // Checkbox
     	  	                $checkbox = '<a href="'.$link_open.'" class="pf_fm_folder_home"><span>&nbsp;</span></a>';

     	  	                // Create HTML output
     	  	                $html .= '<tr class="pf_row'.$k.'"><td class="pf_number_cell">'.($x+1).'</td>';

      	                    if($can_move || $can_delete) $html .= '<td align="center" class="pf_check_cell">'.$checkbox.'</td>';

                            $html .= '<td class="pf_directory_title item_title">
      	                    <a href="'.$link_open.'" class="pf_fm_folder"><span>'.$row->title.'</span></a></td>';

                            $html .= '<td class="pf_actions_cell"></td>';

      	                    if(!$desc_tt) '<td></td>';

      	                    $html .= '<td>'.PFformat::ToDate($row->cdate).'</td>
      	                        <td>'.$row->name.'</td>
      	                        <td class="idcol pf_id_cell">'.$row->id.'</td>
      	                    </tr>';
      	                    $k = 1 - $k;
      	                    $x++;
     	                }
                    }

                    /* END LIST FOLDERS */
                    /* START LIST DOCUMENTS */
                    foreach ($notes AS $i => $row)
 	                {
 	  	                if(!$row->id) continue;
 	  	                JFilterOutput::objectHTMLSafe($row);

 	  	                // Check permission
  	                    $can_move2   = $user->Access('list_move', 'filemanager_pro', $row->author);
  	                    $can_delete2 = $user->Access('task_delete', 'filemanager_pro', $row->author);

 	  	                // Setup Tooltip
 	  	                $ht  = '';
 	  	                $tt  = '';
 	  	                $ast = '';
 	  	                if($desc_tt && trim($row->description) != '') {
                           $ht  = ' hasTip';
                           $tt  = ' title="::'.$row->description.'"';
                           $ast = ' *';
                        }

 	  	                // Setup links
 	  	                $link_open = PFformat::Link("section=filemanager_pro&dir=$dir&task=display_note&id=$row->id");
 	  	                $link_edit = PFformat::Link("section=filemanager_pro&dir=$dir&task=form_edit_note&id=$row->id");
 	  	                $link_nlv  = PFformat::Link("section=filemanager_pro&dir=$dir&task=list_note_versions&id=$row->id");

 	  	                // Checkbox
 	  	                $checkbox = '<input id="cb'.$x.'" name="note[]" value="'.$row->id.'" onclick="isChecked(this.checked);" type="checkbox"/>';

 	  	                // Check access
 	  	                $can_view = $user->Access('display_note', 'filemanager_pro', $row->author);

 	  	                if($can_view) {
                            $row->title = '<a href="'.$link_open.'" class="pf_fm_doc'.$ht.'"'.$tt.'><span>'.$row->title.$ast.'</span></a>';
                        }
                        else {
                            $row->title = '<span class="pf_fm_doc'.$ht.'"'.$tt.'><span>'.$row->title.$ast.'</span></span>';
                        }

 	  	                // Create HTML output
 	  	                $html .= '<tr class="pf_row'.$k.'"><td valign="top" class="pf_number_cell">'.($x+1).'</td>';

 	  	                if($can_move || $can_delete) {
                            if($can_move2 || $can_delete2) {
                                $html .= '<td align="center" class="pf_check_cell">'.$checkbox.'</td>';
                            }
                            else {
                                $html .= '<td align="center" class="pf_check_cell"></td>';
                            }
                        }

 	  	                $html .= '<td class="pf_directory_title item_title" valign="top">'.$row->title;
 	  	                if($note_vc) $html .= '<small class="vc_version">'.PFformat::Lang('PFL_VERSION').': '.$row->version.'</small>';

 	  	                $html .= '</td><td class="pf_actions_cell" align="top">'.$table->Menu();
 	  	                if($row->checked_out && ($row->checked_out_user != $user->GetId()) && $use_checkin) {
 	  	                    $html .= $table->MenuItem('javascript:;', 'MSG_IS_CHECKED_OUT', 'pf_checkedout');
 	  	                }
 	  	                else {
                            if($user->Access('form_edit_note', 'filemanager_pro', $row->author)) $html .= $table->MenuItem($link_edit, 'TT_EDIT', 'pf_edit');
                        }
                        if($nlv) {
                            $html .= $table->MenuItem($link_nlv, 'PFL_LIST_VERSIONS', 'pf_listv');
                        }
  	                    $html .= $table->Menu(false).'</td>';

  	                    if(!$desc_tt) $html .= '<td valign="top">'.$row->description.'</td>';

  	                    $html .= '<td valign="top">'.PFformat::ToDate($row->edate).'</td>
  	                        <td valign="top">'.$row->name.'</td>
  	                        <td class="idcol pf_id_cell" valign="top">'.$row->id.'</td>
                        </tr>';

                        $k = 1 - $k;
  	                    $x++;
 	                }
                    /* END LIST DOCUMENTS */
                    /* START LIST FILES */
                    foreach ($files AS $i => $row)
 	                {
 	  	                if(!$row->id) continue;
 	  	                JFilterOutput::objectHTMLSafe($row);

 	  	                // Check permission
  	                    $can_move2   = $user->Access('list_move', 'filemanager_pro', $row->author);
  	                    $can_delete2 = $user->Access('task_delete', 'filemanager_pro', $row->author);

 	  	                // Setup Tooltip
 	  	                $ht  = '';
 	  	                $tt  = '';
 	  	                $ast = '';
 	  	                if($desc_tt && trim($row->description) != '') {
                           $ht  = ' hasTip';
                           $tt  = ' title="::'.$row->description.'"';
                           $ast = ' *';
                        }

                        // Check file preview
                        $do_preview = false;
                        $is_image   = false;
 	  	                $fname      = $row->prefix.rawurlencode(JFile::makeSafe(strtolower($row->name)));
                        $f_ext      = explode('.',$row->name);
                        $f_ext      = strtolower(end($f_ext));
                        $f_size     = (int) $row->filesize;
                        $imgs       = array('jpg', 'png', 'gif', 'bmp');

                        if(in_array($f_ext, $prev_ext) && ($f_size <= $prev_size || $prev_size == 0)) $do_preview = true;
                        if(in_array($f_ext, $imgs)) $is_image = true;

                        // Setup link
		                $link_preview = $base_url.$upload_url."project_$row->project/$fname";
 	  	                $link_open    = PFformat::Link("section=filemanager_pro&dir=$dir&task=task_download&id=$row->id");
 	  	                $link_edit    = PFformat::Link("section=filemanager_pro&dir=$dir&task=form_edit_file&id=$row->id");
 	  	                $link_flv     = PFformat::Link("section=filemanager_pro&dir=$dir&task=list_file_versions&id=$row->id");

                        // Checkbox
 	  	                $checkbox = '<input id="cb'.$x.'" name="file[]" value="'.$row->id.'" onclick="isChecked(this.checked);" type="checkbox"/>';

 	  	                // Format title
 	  	                $file_type = substr($row->name,-3);
                        if($user->Access('task_download', 'filemanager_pro', $row->author)) {
                            $row->name = '<a href="'.$link_open.'" class="pf_fm_file '.$file_type.$ht.'"'.$tt.'><span>'.$row->name.$ast.'</span></a>';
                        }
                        else {
                            $row->name = '<span class="pf_fm_file '.$file_type.$ht.'"'.$tt.'><span>'.$row->name.$ast.'</span></span>';
                        }

 	  	                $html .= '<tr class="pf_row'.$k.'"><td class="pf_number_cell" valign="top">'.($x+1).'</td>';

 	  	                if($can_move || $can_delete) {
                            if($can_move2 || $can_delete2) {
                                $html .= '<td align="center" class="pf_check_cell">'.$checkbox.'</td>';
                            }
                            else {
                                $html .= '<td align="center" class="pf_check_cell"></td>';
                            }
                        }

 	  	                $html .= '<td valign="top">'.$row->name;

 	  	                if($file_vc) $html .= '<small class="vc_version">'.PFformat::Lang('PFL_VERSION').': '.$row->version.'</small>';

 	  	                $html .= '</td><td class="pf_actions_cell" valign="top">'.$table->Menu();
 	  	                if($row->checked_out && ($row->checked_out_user != $user->GetId()) && $use_checkin) {
 	  	                    $html .= $table->MenuItem('javascript:;', 'MSG_IS_CHECKED_OUT', 'pf_checkedout');
 	  	                }
 	  	                else{
                            if($user->Access('form_edit_file', 'filemanager_pro', $row->author)) $html .= $table->MenuItem($link_edit,'TT_EDIT','pf_edit');
                        }
                        if($flv) {
                            $html .= $table->MenuItem($link_flv, 'PFL_LIST_VERSIONS', 'pf_listv');
                        }
                        if($do_preview) {
                            if($is_image) {
                                $html .= $table->ModalMenuItem($link_preview, 'PFL_PREVIEW', 'pf_preview');
                            }
                            else {
                                $html .= '<li class="pf_preview">
                                    <a rel="{handler: \'iframe\', size: {x: 720, y: 480}}"
                                    href="'.$link_preview.'" class="modal hasTip" title="::'.PFformat::Lang('PFL_PREVIEW').'">
                                    <span>'.PFformat::Lang('PFL_PREVIEW').'</span></a></li>';
                            }
                        }
  	                    $html .= $table->Menu(false).'</td>';

  	                    if(!$desc_tt) $html .= '<td valign="top">'.$row->description.'</td>';

  	                    $html .= '<td valign="top">'.PFformat::ToDate($row->edate).'</td>
  	                        <td valign="top">'.$row->uname.'</td>
  	                        <td class="idcol pf_id_cell" valign="top">'.$row->id.'</td>
  	                    </tr>';

  	                    $k = 1 - $k;
  	                    $x++;
 	                }

 	                /* EMPTY FOLDER */
 	                if($x == 0) {
                        $html .= '<tr class="row0 sectiontableentry1">
                        <td colspan="8" align="center" style="text-align:center"><div class="pf_info">'.PFformat::Lang('EMPTY_FOLDER').'</div></td>
                        </tr>';
                    }
                    /* END LIST FILES */
                    echo $html;
                    unset($html);
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
echo $form->HiddenField('keyword');
echo $form->End();
?>
<script type="text/javascript">
function task_delete()
{
	if(document.adminForm.boxchecked.value == 0) {
		alert('<?php echo PFformat::Lang('ALERT_LIST');?>');
	}
	else {
		submitbutton('task_delete');
	}
}
function list_move()
{
	if(document.adminForm.boxchecked.value == 0) {
		alert('<?php echo PFformat::Lang('ALERT_LIST');?>');
	}
	else {
		submitbutton('list_move');
	}
}
</script>