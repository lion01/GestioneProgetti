<?php
/**
* This file generates the HTML output of the record list
**/

// This line must be at the top of every PHP file to prevent direct access!
defined( '_JEXEC' ) or die( 'Restricted access' );

$core = PFcore::GetInstance();

$user    = PFuser::GetInstance();
$id = (int) JRequest::GetVar('id');


$project = $user->GetWorkspace();
			if(!$project) {
				$project = 0;

				$display_all = true;
			}
			
$db   = PFdatabase::GetInstance();
$cfg  = PFconfig::GetInstance();
					$query = "SELECT title FROM #__pf_projects WHERE id = '$project'";

                    $db->setQuery($query);
					
                    $ptitle = $db->loadResult();


?>

<iframe src="http://commessa.altervista.org/gridV2/index.php?pippo=<?= $ptitle ?>" frameborder="0" height="950" width="100%" marginheight="25px" marginwidth="25px"></iframe>