<?php
/**
* @package   User Status
* @copyright Copyright (C) 2009-2010 DataBeis. All rights reserved.
* @license   GNU/General Public License
**/


defined( '_JEXEC' ) or die( 'Restricted access' );

$db     = PFdatabase::GetInstance();
$config = PFconfig::GetInstance();
$user   = PFuser::GetInstance();
$load   = PFload::GetInstance();

// Config settings
$show_email = $config->Get('show_email', 'cp_teamstatus');

if($show_email == NULL) {
    $config->Set('show_email', '1', 'cp_teamstatus');
    $show_email = 1;
}

// Setup vars
$workspace = $user->GetWorkspace();
$looped    = array();

if($workspace) {
    $query = "SELECT user_id FROM #__pf_project_members"
           . "\n WHERE project_id = '".$workspace."'";
           $db->setQuery($query);
           $users = $db->loadResultArray();

    if(!is_array($users)) $users = array();
    
    foreach($users AS $team_user)
    {
        if(in_array($team_user, $looped)) continue;

        $looped[] = $team_user;
        $albl     = "Status";

        if($team_user == $user->GetId()) $albl = "My Status";

        $query = "SELECT id, name, username, email, lastvisitDate"
               . "\n FROM #__users WHERE id = '$team_user'";
               $db->setQuery($query);
               $tu = $db->loadObject();

        if(!is_object($tu)) continue;
        
        $tu->activity = $user->GetProfile('activity', false, false, $team_user);
        JFilterOutput::objectHTMLSafe($tu);
        ?>
        <div class="team_activity">
            <div>
            	<?php if($tu->activity){?>
            	<div class="activity_status">
            		<div class="activity_status_inner"><?php echo $tu->activity;?></div>
            	</div>
            	<?php } ?>
                <div class="activity_avatar">
                	<?php if($user->Access('display_details', 'profile')) { ?>
                	<?php echo PFavatar::Display($tu->id);?>
                	<div class="myactivity_link">
                	    <a href="<?php echo PFformat::Link("section=profile&task=display_details&id=$tu->id");?>" class="pf_button">
                	    <?php echo PFformat::Lang('VIEW_PROFILE');?>
                	    </a>
                	</div>
                	<?php } else { ?>
                	<?php echo PFavatar::Display($tu->id, false);?>
                	<?php } ?>
                </div>
                <div class="activity_info">
                    <ul class="team_activity_list">
                        <li class="myactivity_name">
                            <span>
                                <strong><?php echo $tu->name;?></strong> (<?php echo $tu->username;?>)
                            </span>
                        </li>
                        <?php if($show_email) { ?>
                        <li class="myactivity_email">
                            <span><?php echo htmlspecialchars($tu->email, ENT_QUOTES);?></span>
                        </li>
                        <?php } ?>
                        <li class="myactivity_date">
                            <span>
                                <strong>Ultimo login:</strong> <?php echo PFformat::ToDate(strtotime($tu->lastvisitDate));?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clr"></div>
        </div>
        <?php
        unset($tu);
    }
}
unset($db, $config, $user, $load);
?>