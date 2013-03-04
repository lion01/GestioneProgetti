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
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo htmlspecialchars($row->title);?></h1>
    </div>
    
    <div class="pf_body">
    
        <!-- TABLE START -->
        <table class="pf_table adminlist" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="10%" class="sectiontableheader title"><?php echo PFformat::Lang('PFL_VERSION'); ?></th>
                    <th width="20%" class="sectiontableheader title"><?php echo PFformat::Lang('TITLE'); ?></th>
                    <th class="sectiontableheader title pf_action_header"><span></span></th>
                    <th width="40%" class="sectiontableheader title"><?php echo PFformat::Lang('DESC'); ?></th>
                    <th width="20%" class="sectiontableheader title"><?php echo PFformat::Lang('AUTHOR'); ?></th>
                    <th width="10%" class="sectiontableheader title"><?php echo PFformat::Lang('DATE'); ?></th>
                </tr>
            </thead>
            <tbody id="table_body adminlist">
                <?php 
                $k     = 0;
                $total = count($rows);
                $html  = '';
                
                foreach ($rows AS $i => $row)
                {
                    JFilterOutput::objectHTMLSafe($row);
                    
                    // Setup links
     	            $link_note    = PFformat::Link("section=filemanager_pro&dir=$dir&task=display_note&id=$row->note_id&v=$row->id");
                    $link_profile = PFformat::Link("section=profile&task=display_details&id=$row->author");
                    $link_compare = PFformat::Link("section=filemanager_pro&dir=$dir&task=form_compare_note&id=$id&n2=$row->id");
                    
                    // Check item access
                    $can_view    = $user->Access('display_note', 'filemanager_pro', $row->author);
                    $can_compare = $user->Access('form_compare_note', 'filemanager_pro', $row->author);
                    $can_profile = $user->Access('display_details', 'profile', $row->author);
                    
                    // Build links
                    if($can_view) {
                        $row->title = '<a href="'.$link_note.'" class="pf_fm_note"><span>'.$row->title.'</span></a>';
                    }
                    else {
                        $row->title = '<span class="pf_fm_note">'.$row->title.'</span>';
                    }
                    
                    if($can_profile) {
                        $row->name = '<a href="'.$link_profile.'">'.$row->name.'</a>';
                    }
                    
                    // Generate output
                    $html .= '<tr class="pf_row'.$k.'">
                    <td>'.($total - $i).'</td>
                    <td>'.$row->title.'</td>
                    <td class="pf_action_cell">';
                    
                    if($can_compare) {
                        $html .= $table->Menu();
                        $html .= $table->MenuItem($link_compare,'COMPARE','pf_compare');
                        $html .= $table->Menu(false);
                    }
                    
                    $html .= '</td>
                    <td>'.$row->description.'</td>
                    <td>'.$row->name.'</td>
                    <td>'.PFformat::ToDate($row->cdate).'</td>
                    </tr>';
     	
     	            $k = 1 - $k;
                }
                
                echo $html;
                ?>
            </tbody>
        </table>
        <!-- TABLE END -->

    </div>
</div>
<?php echo $form->End(); ?>