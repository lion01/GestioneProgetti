<?php

/*

* This is the entry file for your panel and may contain all your code/html output.

* If the panel produces no output, the panel won't be shown at all (including the html code that wraps it)

*/



// This line must be at the top of every PHP file to prevent direct access!

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');

$j_uri    = & JFactory::getURI();
$base_url = $j_uri->base();
$base_url = str_replace('administrator/', '', $base_url);

$core = PFcore::GetInstance();



$db   = PFdatabase::GetInstance();



$user = PFuser::GetInstance();



$cfg  = PFconfig::GetInstance();







$uid  = $user->GetId();



$ws   = $user->GetWorkspace();







// Don't show anything unless we are logged in



if(!$uid) return false;



$cfg = PFconfig::GetInstance();

function LoadFileListAsad($workspace = 0)



	{



	    $db = PFdatabase::GetInstance();
		
		$user = PFuser::GetInstance();
		

		$projects = $workspace;

		$all_projects = false;



		if(!$workspace) {



			$projects = $user->Permission('projects');

		

			$projects = implode(',',$projects);

		

			$all_projects = true;		

		}

		

		

		$filter = ($all_projects == true) ? "\n AND f.project IN($projects)" : "\n AND f.project = $projects";

		

		$query = "SELECT f.*, u.name AS uname, u.id AS uid, p.checked_out,"



               . "\n p.checked_out_user, p.locked, p.locked_user, p.status, COUNT(v.id) AS version"



               . "\n FROM #__pf_files AS f"



		       . "\n LEFT JOIN #__users AS u ON u.id = f.author"



               . "\n LEFT JOIN #__pf_file_properties AS p ON p.file_id = f.id"



               . "\n LEFT JOIN #__pf_file_versions AS v ON v.file_id = f.id"
			   //. "\n LEFT JOIN #__pf_folders AS f"



		       . "\n WHERE  1=1 "



		       . $filter



               . "\n GROUP BY f.id"



		       . "\n ORDER BY f.id DESC";



		       $db->setQuery($query);



		       $rows = $db->loadObjectList();







		if(!is_array($rows)) $rows = array();







		return $rows;



	}





$limit    = (int) $cfg->Get('limit', 'files_panel');



$files = LoadFileListAsad($ws);



$html = '<table class="adminlist pf_table files_panel_table" width="100%" cellpadding="0" cellspacing="0">



         <tbody>';

		 if(!$files)$html .= '<tr><td>No files</td></tr>';

foreach ($files AS $i => $row)

	{

		if(!$row->id) continue;

		JFilterOutput::objectHTMLSafe($row);


		$dir = $row->dir;
		



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



		//if(in_array($f_ext, $prev_ext) && ($f_size <= $prev_size || $prev_size == 0)) $do_preview = true;

		if(in_array($f_ext, $imgs)) $is_image = true;



		// Setup link

		$link_preview = $base_url.$upload_url."project_$row->project/$fname";

		$link_open    = PFformat::Link("section=filemanager_pro&dir=$dir&task=task_download&id=$row->id");

		$link_edit    = PFformat::Link("section=filemanager_pro&dir=$dir&task=form_edit_file&id=$row->id");

		$link_flv     = PFformat::Link("section=filemanager_pro&dir=$dir&task=list_file_versions&id=$row->id");


		// Format title

		$file_type = substr($row->name,-3);

		if($user->Access('task_download', 'filemanager_pro', $row->author)) {

			$row->name = '<a href="'.$link_open.'" class="pf_fm_file '.$file_type.$ht.'"'.$tt.'><span>'.$row->name.$ast.'</span></a>';

		}

		else {

			$row->name = '<span class="pf_fm_file '.$file_type.$ht.'"'.$tt.'><span>'.$row->name.$ast.'</span></span>';

		}



		$html .= '<tr class="pf_row'.$k.'"><td class="pf_number_cell" valign="top">'.($x+1).'</td>';


		$html .= '<td valign="top">'.$row->name;



		if($file_vc) $html .= '<small class="vc_version">'.PFformat::Lang('PFL_VERSION').': '.$row->version.'</small>';



		$html .= '</td>';

		if($row->checked_out && ($row->checked_out_user != $user->GetId()) && $use_checkin) {

			$html .= $table->MenuItem('javascript:;', 'MSG_IS_CHECKED_OUT', 'pf_checkedout');

		}


		if($flv) {



			$html .= $table->MenuItem($link_flv, 'PFL_LIST_VERSIONS', 'pf_listv');

		}


		$html .= '</td>';



		if(!$desc_tt) $html .= '<td valign="top">'.$row->description.'</td>';



		$html .= '<td valign="top">'.PFformat::ToDate($row->edate).'</td>

			<td valign="top">'.$row->uname.'</td>

			<td class="idcol pf_id_cell" valign="top">'.$row->id.'</td>

		</tr>';



		$k = 1 - $k;

		$x++;

	}







$html .= '<tfoot>



     



     </tfoot></table>';



     



echo $html;     









?>