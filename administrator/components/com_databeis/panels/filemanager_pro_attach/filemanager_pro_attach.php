<?php
/**
* @package   File Manager Pro
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$id = (int) JRequest::getVar('id');
$db = PFdatabase::GetInstance();

if($id) {
	// Get connected folders
	$query = "SELECT f.* FROM #__pf_task_attachments AS a"
	       . "\n RIGHT JOIN #__pf_folders AS f ON f.id = a.attach_id"
	       . "\n WHERE a.task_id = '$id' AND a.attach_type = 'folder'"
	       . "\n GROUP BY a.attach_id ORDER BY f.title";
	       $db->setQuery($query);
	       $folders = $db->loadObjectList();
	       
	if(!is_array($folders)) $folders = array();

	// Get connected notes
	$query = "SELECT n.* FROM #__pf_task_attachments AS a"
	       . "\n RIGHT JOIN #__pf_notes AS n ON n.id = a.attach_id"
	       . "\n WHERE a.task_id = '$id' AND a.attach_type = 'note'"
	       . "\n GROUP BY a.attach_id ORDER BY n.title";
	       $db->setQuery($query);
	       $notes = $db->loadObjectList();
	       
	if(!is_array($notes)) $notes = array();
	
	// Get connected files
	$query = "SELECT f.* FROM #__pf_task_attachments AS a"
	       . "\n RIGHT JOIN #__pf_files AS f ON f.id = a.attach_id"
	       . "\n WHERE a.task_id = '$id' AND a.attach_type = 'file'"
	       . "\n GROUP BY a.attach_id ORDER BY f.name";
	       $db->setQuery($query);
	       $files = $db->loadObjectList();
	       
	if(!is_array($files)) $files = array();
	
	// Show attachments
	if(count($folders) || count($notes) || count($files)) {
		$k = 0;
		?>
		<table class="pf_table adminlist" width="100%" cellpadding="0" cellspacing="0">
		    <tbody>
		    <?php 
		    // Folders
            foreach ($folders AS $row)
		    {
		        JFilterOutput::objectHTMLSafe($row);
		      	$link_open = PFformat::Link("section=filemanager_pro&dir=$row->id");
		      	?>
		      	<tr class="pf_row<?php echo $k;?>">
		      	    <td width="40%" valign="top"><a href="<?php echo $link_open;?>" class="pf_fm_folder"><?php echo $row->title; ?></a></td>
		      	    <td><?php echo $row->description;?></td>
		      	</tr>
		      	<?php
		      	$k = 1 - $k;
		    }
		    
		    // Notes
		    foreach ($notes AS $row)
		    {
		        JFilterOutput::objectHTMLSafe($row);
		      	$link_open = PFformat::Link("section=filemanager_pro&dir=$row->dir&task=display_note&id=$row->id");
		      	?>
		      	<tr class="pf_row<?php echo $k;?>">
		      	    <td width="40%" valign="top"><a href="<?php echo $link_open;?>" class="pf_fm_doc"><?php echo $row->title; ?></a></td>
		      	    <td><?php echo $row->description;?></td>
		      	</tr>
		      	<?php
		      	$k = 1 - $k;
		    }
		     
            // Files  
		    foreach ($files AS $row)
		    {
		        JFilterOutput::objectHTMLSafe($row);

		      	$link_open = PFformat::Link("section=filemanager_pro&dir=$row->dir&task=task_download&id=$row->id");
		      	?>
		      	<tr class="pf_row<?php echo $k;?>">
		      	    <td width="40%" valign="top"><a href="<?php echo $link_open;?>" class="pf_fm_file"><?php echo $row->name; ?></a></td>
		      	    <td><?php echo $row->description;?></td>
		      	</tr>
		      	<?php
		      	$k = 1 - $k;
		    }
		    ?>
		    </tbody>
		</table>
		<?php
	}
}
?>