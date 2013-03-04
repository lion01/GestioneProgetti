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
        <h1><?php echo $ws_title." / "; echo PFformat::Lang('FILEMANAGER');?> :: <?php echo htmlspecialchars($row->name);?></h1>
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
     	            $link_file    = PFformat::Link("section=filemanager_pro&dir=$dir&task=task_download&id=$row->file_id&v=$row->id");
                    $link_profile = PFformat::Link("section=profile&task=display_details&id=$row->author");
                    
                    // Check item access
                    $can_view    = $user->Access('task_download', 'filemanager_pro', $row->author);
                    $can_profile = $user->Access('display_details', 'profile', $row->author);
                    
                    // Build links
                    $file_type = substr($row->name,-3);
                    if($can_view) {
                        $row->name = '<a href="'.$link_file.'" class="pf_fm_file '.$file_type.'"><span>'.$row->name.'</span></a>';
                    }
                    else {
                        $row->name = '<span class="pf_fm_file '.$file_type.'">'.$row->name.'</span>';
                    }
                    
                    if($can_profile) {
                        $row->authorname = '<a href="'.$link_profile.'">'.$row->authorname.'</a>';
                    }
                    
                    // Generate output
                    $html .= '<tr class="pf_row'.$k.'">
                    <td>'.($total - $i).'</td>
                    <td>'.$row->name.'</td>
                    <td class="pf_action_cell">';
                    
                    $html .= '</td>
                    <td>'.$row->description.'</td>
                    <td>'.$row->authorname.'</td>
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